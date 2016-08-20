<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: figurelib/submit.php based on weblink_submit.php
| Author: Frederick MC Chan (Chan)
| 
| Modification: Catzenjaeger
| URL: www.aliencollectors.com
| E-Mail: admin@aliencollectors.com
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
//if (!defined("IN_FUSION")) { die("Access Denied"); }
require_once "../../maincore.php";
include "infusion_db.php";
require_once THEMES . "templates/header.php";
require_once INCLUDES."html_buttons_include.php";
require_once INCLUDES."infusions_include.php";
// get settings
$fil_settings = get_settings("figurelib");
// get language
if (file_exists(INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php")) {
    include INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php";
} else {
    include INFUSIONS."figurelib/locale/English/locale_figurelib.php";
}
// ['figure_521'] = "Submit Figure";
add_to_title($locale['global_200'].$locale['figure_521']);
openside("<i class='fa fa-globe fa-lg m-r-10'></i>".$locale['figure_521']);
// Can members submit?
if (iMEMBER && $fil_settings['figure_submit']) {
	
	// Check if there exists Cats
	if (dbcount("(figure_cat_id)", DB_FIGURE_CATS, multilang_table("FI") ? "figure_cat_language='".LANGUAGE."'" : "")) {
	
	// Standard Values for Fields
	$submitdata = array(
			"figure_freigabe" => 0, 
			"figure_title" => "", 
			"figure_variant" => "", 
			"figure_manufacturer" => "",
			"figure_artists" => "", 
			"figure_country" => "", 
			"figure_brand" => "", 
			"figure_series" => "", 
			"figure_scale" => "",
			"figure_weight" => "", 
			"figure_height" => "", 
			"figure_width" => "", 
			"figure_depth" => "", 
			"figure_material" => "", 
			"figure_poa" => "", 
			"figure_packaging" => "", 
			"figure_retailprice" => "", 
			"figure_usedprice" => "", 
			"figure_limitation" => "",
			"figure_cat" => "",
			"figure_editionsize" => "", 
			"figure_accessories" => "", 
			"figure_description" => "", 
			"figure_pubdate" => "", 
			"figure_agb" => 0,
			"figure_datestamp" => "",
			"figure_submitter" => $userdata['user_id'],		
	);
		// Form posted
		if (isset($_POST['submit_figure'])) {
			
			// Check Fields
			$submitdata = array(	
				"figure_datestamp"    => form_sanitizer($_POST['figure_datestamp'],    "", "figure_datestamp"),			
				"figure_freigabe"     => form_sanitizer($_POST['figure_freigabe'],     0,  "figure_freigabe"),
				"figure_title"        => form_sanitizer($_POST['figure_title'],        "", "figure_title"),
				"figure_variant"      => form_sanitizer($_POST['figure_variant'],      "", "figure_variant"),
				"figure_manufacturer" => form_sanitizer($_POST['figure_manufacturer'], "", "figure_manufacturer"),
				"figure_artists"      => form_sanitizer($_POST['figure_artists'],      "", "figure_artists"),
				"figure_country"      => form_sanitizer($_POST['figure_country'],      "", "figure_country"),
				"figure_brand"        => form_sanitizer($_POST['figure_brand'],        "", "figure_brand"),
				"figure_series"       => form_sanitizer($_POST['figure_series'],       "", "figure_series"),
				"figure_scale"        => form_sanitizer($_POST['figure_scale'],        "", "figure_scale"),
				"figure_weight"       => form_sanitizer($_POST['figure_weight'],       "", "figure_weight"),
				"figure_height"       => form_sanitizer($_POST['figure_height'],       "", "figure_height"),
				"figure_width"        => form_sanitizer($_POST['figure_width'],        "", "figure_width"),
				"figure_depth"        => form_sanitizer($_POST['figure_depth'],        "", "figure_depth"),
				"figure_material"     => form_sanitizer($_POST['figure_material'],     "", "figure_material"),
				"figure_poa"          => form_sanitizer($_POST['figure_poa'],          "", "figure_poa"),
				"figure_packaging"    => form_sanitizer($_POST['figure_packaging'],    "", "figure_packaging"),
				"figure_retailprice"  => form_sanitizer($_POST['figure_retailprice'],  "", "figure_retailprice"),
				"figure_usedprice"    => form_sanitizer($_POST['figure_usedprice'],    "", "figure_usedprice"),
				"figure_limitation"   => form_sanitizer($_POST['figure_limitation'],   "", "figure_limitation"),
				"figure_cat"          => form_sanitizer($_POST['figure_cat'],          "", "figure_cat"),
				"figure_editionsize"  => form_sanitizer($_POST['figure_editionsize'],  "", "figure_editionsize"),
				"figure_pubdate"      => form_sanitizer($_POST['figure_pubdate'],      "", "figure_pubdate"),
				"figure_agb"          => form_sanitizer($_POST['figure_agb'],          0,  "figure_agb"),
				"figure_submitter"    => form_sanitizer($_POST['figure_submitter'],    "", "figure_submitter"), 
				"figure_description"  => addslash(nl2br(parseubb(stripinput($_POST['figure_description'])))),
				"figure_accessories"  => addslash(nl2br(parseubb(stripinput($_POST['figure_accessories'])))),			
			);
			
			// Check double Entry
			if (dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_title='".$submitdata['figure_title']."' AND figure_cat='".$submitdata['figure_cat']."'")) {
				$defender -> setInputError("figure_title");
				$defender -> setErrorText("figure_title", "There is a submitted Figure with this Name in the same Category!");
				defender::stop();
			}
			
			// Insert Figure data 
			if (defender::safe()) {
				dbquery_insert(DB_FIGURE_ITEMS, $submitdata, "save", array("keep_session"=>true));
				$unique_id = dblastid();
				$submissionFAILURE = false;
				
				// Image upload
				if (!empty($_FILES['figure_image'])) {
					
					// To pair with the image into the database.
					$image_data = (array) form_sanitizer($_FILES['figure_image'], "", "figure_image");
					if (!empty($image_data)) {
						foreach($image_data as $image) {
							$i_data = array(
								"figure_images_image_id"  => 0,
							    "figure_images_figure_id" => $unique_id,
							    "figure_images_image"     => $image['image_name'],
								"figure_images_thumb"     => $image['thumb1_name'],
								"figure_images_sorting"   => "",
								"figure_images_language"  => LANGUAGE,
							);
							dbquery_insert(DB_FIGURE_IMAGES, $i_data, "save", array("keep_session" => true));
							
							// Check if there are some Errors
							if ($image['error'] != "0") {
								$submissionFAILURE = true;
								break;
							}
						}
					} else {
						$submissionFAILURE = true;
					}
				} else {
					$submissionFAILURE = true;
				}
        
				// If there are some Errors, delete all
				if ($submissionFAILURE) {
					
					// Delete Figure
					dbquery("DELETE FROM ".DB_FIGURE_ITEMS." WHERE figure_id='".$unique_id."'");
					
					// Delete Images
					$resultImages = dbquery("SELECT figure_images_image, figure_images_thumb FROM ".DB_FIGURE_IMAGES." WHERE figure_images_figure_id='".$unique_id."'");
					if (dbrows($resultImages)) {
						while ($dataImages) {
							if ($dataImages['figure_images_thumb'] && @file_exists(THUMBS_FIGURES.$dataImages['figure_images_thumb'])) {
								@unlink(THUMBS_FIGURES.$dataImages['figure_images_thumb']);
							}
							if ($dataImages['figure_images_image'] && @file_exists(IMAGES_FIGURES.$dataImages['figure_images_image'])) {
								@unlink(IMAGES_FIGURES.$dataImages['figure_images_image']);
							}
						}
						dbquery("DELETE FROM ".DB_FIGURE_IMAGES."  WHERE figure_images_figure_id='".$unique_id."'");
					}
					
				// Otherwise display Success Message, send Notification and redirect.
				} else {
					addNotice("success", $locale['figs_0018']);
				
					// Send Notification
					if ($fil_settings['figure_notification'] != "1") {
						$fil_settings['figure_notification_message'] = str_replace("{FIGURELINK}", $settings['siteurl']."infusions/figurelib/figures.php?figure_id=".$unique_id."&action=admincatcher", $fil_settings['figure_notification_message']);
						
						// Send Message Notification
						if ($fil_settings['figure_notification'] == "2") {
							require_once INCLUDES."infusions_include.php";
							send_pm("-103", $userdata['user_id'], $fil_settings['figure_notification_subject'], $fil_settings['figure_notification_message'], "y", true);

						// Send Mail Notification
						} elseif ($fil_settings['figure_notification'] == "3") {
							$resultAdmins = dbquery("SELECT user_name, user_email FROM ".DB_USERS." WHERE user_id != '".$userdata['user_id']."' AND user_level='-103' AND user_status='0'");
							if (dbrows($resultAdmins)) {
								require_once INCLUDES."sendmail_include.php";
								$fil_settings['figure_notification_message'] = nl2br(parseubb(parsesmileys($fil_settings['figure_notification_message'])));
								while ($dataAdmin = dbarray($resultAdmins)) {
									sendemail($dataAdmin['user_name'], $dataAdmin['user_email'], $userdata['user_name'], $userdata['user_email'], $fil_settings['figure_notification_subject'], $fil_settings['figure_notification_message'], "html");
								}
							}
						}
					}
				
					// Redirect
					redirect(clean_request("submitted=f", array("stype"), TRUE));
				}
				
			}
		
		}	
	
		if (isset($_GET['submitted']) && $_GET['submitted'] == "f") {
           // ['figs_0018'] = "Thank you for submitting your Figure";
		   echo "<div class='well text-center'><p><strong>".$locale['figs_0018']."</strong></p>";
			
			// ['figs_0019'] = "Submit another Figure";
			//echo "<p><a href='submit.php?stype=f'>".$locale['figs_0019']."</a></p>";
            echo "<p><a href='".INFUSIONS."figurelib/submit.php?stype=f'>".$locale['figs_0019']."</a></p>";
						
			// ['figs_0020'] = "Return to [SITENAME]";
			echo "<p><a href='".BASEDIR."home.php'>".str_replace("[SITENAME]", fusion_get_settings("sitename"), $locale['figs_0020'])."</a></p>\n";
			echo "</div>\n";
        } else {
			echo "<div class='panel panel-default tbl-border'>\n<div class='panel-body'>\n";
			
			// ['figure_532'] = "Use the following form to submit a Alien figure. Your submission will be reviewed by an ::
			echo "<div class='alert alert-info m-b-20 submission-guidelines'>".str_replace("[SITENAME]", fusion_get_settings("sitename"),$locale['figure_532'])."</div>\n";
            //echo openform('submit_form', 'post', BASEDIR."submit.php?stype=f");
			
			//echo openform('submit_form', 'post', FUSION_REQUEST, array("class" => "m-t-20", "enctype"=>true));
			echo openform('submit_form', 'post', INFUSIONS."figurelib/submit.php?stype=f", array("class" => "m-t-20", "enctype"=>true));			
			//echo openform('submit_form', 'post', FUSION_REQUEST, array("class" => "m-t-20", "enctype"=>true));
		
		// Hidden Field "Freigabe"
		echo form_hidden("figure_freigabe", "", "0");
		
		// Hidden Field "Submitter"
		echo form_hidden("figure_submitter", "", $userdata['user_id']);
		
		// Hidden Field "Datestamp"
		echo form_hidden("figure_datestamp", "", time());
		
		// Select Field "Category"
		echo form_select_tree("figure_cat", $locale['figure_413'], $submitdata['figure_cat'], 
			array(
				"inline" => TRUE,
				"width" => "400px",
				"required" => TRUE,
				"placeholder" => $locale['figurelib-placeholder-101'],
				"error_text" => $locale['figurelib/admin/figurelib.php_070'],
				"no_root" => 1,
				"query" => (multilang_table("FI") ? "WHERE figure_cat_language='".LANGUAGE."'" : ""),
				"maxselect" => 1,
				"allowclear" => TRUE,
			),
		DB_FIGURE_CATS, "figure_cat_name", "figure_cat_id", "figure_cat_parent");
		// Text Field "Title"
		echo form_text("figure_title", $locale['figure_411'], $submitdata['figure_title'],
			array(
				"inline" => TRUE,
				"required" => TRUE,
				"width" => "400px",
				"max_length" => 320,
				"placeholder" => $locale['figure_1801'],
				"error_text" => $locale['figurelib-error-101']
			)
		);
		// Text Field "Variant"
		echo form_text("figure_variant", $locale['figure_441'], $submitdata['figure_variant'],
			array(
				"inline" => TRUE,
				"width" => "400px",
				"max_length" => 320,
				"placeholder" => $locale['figure_1802']
			)
		);
		// Select Field "Manufacturer"
		echo form_select_tree("figure_manufacturer", $locale['figure_417'], $submitdata['figure_manufacturer'], 
			array(
				"inline" => TRUE,
				"required" => TRUE,
				"width" => "400px",
				"placeholder" => $locale['figurelib-placeholder-102'],
				"error_text" => $locale['figurelib-error-102'],
				"no_root" => 1,
				"query" => (multilang_table("FI") ? "WHERE figure_manufacturer_language='".LANGUAGE."'" : ""),
				"maxselect" => 1,
				"allowclear" => TRUE,
			),
		DB_FIGURE_MANUFACTURERS, "figure_manufacturer_name", "figure_manufacturer_id", "figure_manufacturer_parent");
		// Text Field "Artists"
		echo form_text("figure_artists", $locale['figure_452'], $submitdata['figure_artists'],
			array(
				"inline" => TRUE,
				"width" => "400px",
				"placeholder" => $locale['figure_1803']
			)
		);
		// Text Field "Country"
		echo form_text("figure_country", $locale['figure_436'], $submitdata['figure_country'],
			array(
				"inline" => TRUE,
				"max_length" => 100,
				"width" => "400px",
				"placeholder" => $locale['figure_1804']
			)
		);
		// Select Field "Brand"
		echo form_select_tree("figure_brand", $locale['figure_438'], $submitdata['figure_brand'], 
			array(
				"inline" => TRUE,
				"required" => TRUE,
				"width" => "400px",
				"placeholder" => $locale['figurelib-placeholder-103'],
				"error_text" => $locale['figurelib-error-103'],
				"no_root" => 1,
				"query" => (multilang_table("FI") ? "WHERE figure_brand_language='".LANGUAGE."'" : ""),
				"maxselect" => 1,
				"allowclear" => TRUE,
			),
		DB_FIGURE_BRANDS, "figure_brand_name", "figure_brand_id", "figure_brand_parent");
		// Text Field "Series"
		echo form_text("figure_series", $locale['figure_439'], $submitdata['figure_series'],
			array(
				"inline" => TRUE,
				"width" => "400px",
				"placeholder" => $locale['figure_1805']
			)
		);
		// Form "Space"
		echo "<div class='tbl1'>\n";
			echo "<hr>\n";
		echo "</div>\n";
		// Select Field "Scale"
		echo form_select_tree("figure_scale", $locale['figure_442'], $submitdata['figure_scale'], 
			array(
				"inline" => TRUE,
				"required" => TRUE,
				"width" => "400px",
				"placeholder" => $locale['figurelib-placeholder-104'],
				"error_text" => $locale['figurelib-error-104'],
				"no_root" => 1,
				"query" => (multilang_table("FI") ? "WHERE figure_scale_language='".LANGUAGE."'" : ""),
				"maxselect" => 1,
				"allowclear" => TRUE,
			),
		DB_FIGURE_SCALES, "figure_scale_name", "figure_scale_id", "figure_scale_parent");
		// Text Field "Weight"
		echo form_text("figure_weight", $locale['figure_443'], $submitdata['figure_weight'],
			array(
				"inline" => TRUE,
				"width" => "400px",
				"max_length" => 40,
				"error_text" => $locale['figurelib-error-113'],
				"placeholder" => $locale['figure_1806']
			)
		);
		// Select Field "Height"
		echo form_select_tree("figure_height", $locale['figure_444'], $submitdata['figure_height'], 
			array(
				"inline" => TRUE,
				"width" => "400px",
				"required" => TRUE,
				"placeholder" => $locale['figurelib-placeholder-105'],
				"error_text" => $locale['figurelib/admin/figurelib.php_031'],
				"no_root" => 1,
				"query" => (multilang_table("FI") ? "WHERE figure_measurements_language='".LANGUAGE."'" : ""),
				"maxselect" => 1,
				"allowclear" => TRUE,
			),
		DB_FIGURE_MEASUREMENTS, "figure_measurements_inch", "figure_measurements_id", "figure_measurements_parent");
		// Select Field "Width"
		echo form_select_tree("figure_width", $locale['figure_445'], $submitdata['figure_width'], 
			array(
				"inline" => TRUE,
				"width" => "400px",
				"required" => TRUE,
				"placeholder" => $locale['figurelib-placeholder-106'],
				"error_text" => $locale['figurelib/admin/figurelib.php_034'],
				"no_root" => 1,
				"query" => (multilang_table("FI") ? "WHERE figure_measurements_language='".LANGUAGE."'" : ""),
				"maxselect" => 1,
				"allowclear" => TRUE,
			),
		DB_FIGURE_MEASUREMENTS, "figure_measurements_inch", "figure_measurements_id", "figure_measurements_parent");
		// Select Field "Depth"
		echo form_select_tree("figure_depth", $locale['figure_446'], $submitdata['figure_depth'], 
			array(
				"inline" => TRUE,
				"required" => TRUE,
				"width" => "400px",
				"placeholder" => $locale['figurelib-placeholder-107'],
				"error_text" => $locale['figurelib-error-105'],
				"no_root" => 1,
				"query" => (multilang_table("FI") ? "WHERE figure_measurements_language='".LANGUAGE."'" : ""),
				"maxselect" => 1,
				"allowclear" => TRUE,
			),
		DB_FIGURE_MEASUREMENTS, "figure_measurements_inch", "figure_measurements_id", "figure_measurements_parent");
		
		// Form "Space"
		echo "<div class='tbl1'>\n";
			echo "<hr>\n";
		echo "</div>\n";
		// Select Field "Material"
		echo form_select_tree("figure_material", $locale['figure_447'], $submitdata['figure_material'], 
			array(
				"inline" => TRUE,
				"required" => TRUE,
				"width" => "400px",
				"placeholder" => $locale['figurelib-placeholder-108'],
				"error_text" => $locale['figurelib-error-106'],
				"no_root" => 1,
				"query" => (multilang_table("FI") ? "WHERE figure_material_language='".LANGUAGE."'" : ""),
				"maxselect" => 1,
				"allowclear" => TRUE,
			),
		DB_FIGURE_MATERIALS, "figure_material_name", "figure_material_id", "figure_material_parent");
		// Select Field "POA"
		echo form_select_tree("figure_poa", $locale['figure_455'], $submitdata['figure_poa'], 
			array(
				"inline" => TRUE,
				"required" => TRUE,
				"width" => "400px",
				"placeholder" => $locale['figurelib-placeholder-109'],
				"error_text" => $locale['figurelib-error-107'],
				"no_root" => 1,
				"query" => (multilang_table("FI") ? "WHERE figure_poa_language='".LANGUAGE."'" : ""),
				"maxselect" => 1,
				"allowclear" => TRUE,
			),
		DB_FIGURE_POAS, "figure_poa_name", "figure_poa_id", "figure_poa_parent");
		// Select Field "Packaging"
		echo form_select_tree("figure_packaging", $locale['figure_448'], $submitdata['figure_packaging'], 
			array(
				"inline" => TRUE,
				"required" => TRUE,
				"width" => "400px",
				"placeholder" => $locale['figurelib-placeholder-110'],
				"error_text" => $locale['figurelib-error-108'],
				"no_root" => 1,
				"query" => (multilang_table("FI") ? "WHERE figure_packaging_language='".LANGUAGE."'" : ""),
				"maxselect" => 1,
				"allowclear" => TRUE,
			),
		DB_FIGURE_PACKAGINGS, "figure_packaging_name", "figure_packaging_id", "figure_packaging_parent");
		// Form "Space"
		echo "<div class='tbl1'>\n";
			echo "<hr>\n";
		echo "</div>\n";
		// Select Field "Pub Date"
		echo form_select_tree("figure_pubdate", $locale['figure_419'], $submitdata['figure_pubdate'], 
			array(
				"inline" => TRUE,
				"required" => TRUE,
				"width" => "400px",
				"placeholder" => $locale['figurelib-placeholder-111'],
				"error_text" => $locale['figurelib-error-109'],
				"no_root" => 1,
				"query" => (multilang_table("FI") ? "WHERE figure_year_language='".LANGUAGE."'" : ""),
				"maxselect" => 1,
				"allowclear" => TRUE,
			),
		DB_FIGURE_YEARS, "figure_year", "figure_year_id", "figure_year_parent");
		
		// Text Field "Retail Price"
		echo form_text("figure_retailprice", $locale['figure_449'], $submitdata['figure_retailprice'],
			array(
				"inline" => TRUE,
				"width" => "368px",
				"error_text" => $locale['figure_1814'],
				"min" => "0",
				"placeholder" => $locale['figure_1807'],
				"prepend_value" => "&#036;",
				"type" => "number"
			)
		);
		
		// Text Field "Used Price"
		echo form_text("figure_usedprice", $locale['figure_456'], $submitdata['figure_usedprice'],
            array(
                "inline" => TRUE,
                "width" => "368px",
                "error_text" => $locale['figure_1815'],
                "placeholder" => $locale['figure_1808'],
				"min" => "0",
                "prepend_value" => "&#036;",
				"type" => "number"
            )
        );
			
		// Form "Space"
		echo "<div class='tbl1'>\n";
			echo "<hr>\n";
		echo "</div>\n";
		
		// Select Field "Limited Edition"
		echo form_select_tree("figure_limitation", $locale['figure_450'], $submitdata['figure_limitation'], 
			array(
				"inline" => TRUE,
				"required" => TRUE,
				"width" => "400px",
				"placeholder" => $locale['figurelib-placeholder-112'],
				"error_text" => $locale['figurelib-error-111'],
				"no_root" => 1,
				"query" => (multilang_table("FI") ? "WHERE figure_limitation_language='".LANGUAGE."'" : ""),
				"maxselect" => 1,
				"allowclear" => TRUE,
			),
		DB_FIGURE_LIMITATIONS, "figure_limitation_name", "figure_limitation_id", "figure_limitation_parent");
		// Text Field "Editions Size"
		echo form_text("figure_editionsize", $locale['figure_451'], $submitdata['figure_editionsize'],
			array(
				"inline" => TRUE,
				"width" => "400px",
				"min" => "1",
				"error_text" => $locale['figure_1816'],
				"placeholder" => $locale['figure_1809'],
				"type" => "number"
			)
		);
		// Form "Space"
		echo "<div class='tbl1'>\n";
			echo "<hr>\n";
		echo "</div>\n";
				
		// File Field "Images"
		//$max_image_allowed = 10;
		$max_image_allowed = $asettings['figure_image_upload_count'];
		echo form_fileinput("figure_image[]", $locale['figure_136'], "", 
			array(
				"inline" => TRUE,
				"template" => "modern",
				"multiple" => TRUE,
				"upload_path" => INFUSIONS."figurelib/figures/images/",
				"thumbnail_folder" => "thumbs/",
				"thumbnail" => TRUE,
				"required" => FALSE,
				'ext_tip' => sprintf($locale['figure_035'], parsebytesize($asettings['figure_photo_max_b']) . $locale['figure_036'] . $max_image_allowed),
				"max_byte" => $asettings['figure_photo_max_b'],
				"max_count" => $max_image_allowed
			)
		);	
		
	
		// Form "Space"
		echo "<div class='tbl1'>\n";
			echo "<hr>\n";
		echo "</div>\n";
		
		// Text Area "Accessories"
		echo form_textarea("figure_accessories", $locale['figure_457'], $submitdata['figure_accessories'],
			array(
				"type" => fusion_get_settings("tinymce_enabled") ? "tinymce" : "html",
				"tinymce" => fusion_get_settings("tinymce_enabled") && iADMIN ? "advanced" : "simple",
				"autosize" => TRUE,
				"required" => FALSE,
				"form_name" => "submit_form",
			)
		);
		// Text Area "Description"
		echo form_textarea("figure_description", $locale['figure_423'], $submitdata['figure_description'],
			array(
				"type" => fusion_get_settings("tinymce_enabled") ? "tinymce" : "html",
				"tinymce" => fusion_get_settings("tinymce_enabled") && iADMIN ? "advanced" : "simple",
				"autosize" => TRUE,
				"required" => FALSE,
				"form_name" => "submit_form",
			)
		);
		// Form "Space"
		echo "<div class='tbl1'>\n";
			echo "<hr>\n";
		echo "</div>\n";
		// Checkbox "Terms"
		echo form_checkbox("figure_agb", $locale['figure_1810'], $submitdata['figure_agb'],
			array(
				"inline" => TRUE,
				"required" => TRUE,
				"error_text" => $locale['figurelib-error-112']
			)
		);
		
		// Form "Space"
		echo "<div class='tbl1'>\n";
			echo "<hr>\n";
		echo "</div>\n";
		
		  // ['figure_521'] = "Submit Figure";
			echo form_button("submit_figure", $locale['figure_521'], $locale['figure_521'], array("class" => "btn-primary"));
			echo closeform();
				
				// message to admins - dosent work at moment
				//require_once(INCLUDES."infusions_include.php");
				
				//    if (isset($_GET['pm'])) {
					require_once(INCLUDES."infusions_include.php");
				//	send_pm("1", "2", "Hi - Subject", "Your Message", "y", "FALSE");
				//	echo "PM sent";
				//	}
				
				
				//self::send_pm(-103, 1, "Figure Submission", "Figure Submission is done", "y", TRUE);
				//send_pm(-103, 1, "Figure Submission", "Figure Submission is done", "y", TRUE);
				
			echo "</div>\n</div>\n";
		}
	} else {
		
		// ['figure_1812'] = "There are no figure categories defined";
		// ['figurelib/admin/figurelib.php_066'] = "You must define at least one category before you can add any figure";
		echo "<div class='well text-center'>\n".$locale['figure_1812']."<br />\n".$locale['figurelib/admin/figurelib.php_066']."</div>\n";
	}
} else {
	
	// ['figure_1813'] = "Sorry, we currently do not accept any figure submissions on this site.";
	echo "<div class='well text-center'>".$locale['figure_1813']."</div>\n";
}
closeside();
require_once THEMES."templates/footer.php";
//send_pm( -102, 1, 'Figure submittet', 'A new figure was submittet!', 'y', TRUE);
//send_pm( -103, 1, 'Figure submittet', 'A new figure was submittet!', 'y', TRUE);
