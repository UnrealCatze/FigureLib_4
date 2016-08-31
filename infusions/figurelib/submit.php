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

// Systemfile
require_once "../../maincore.php";
include "infusion_db.php";
require_once THEMES . "templates/header.php";
require_once INCLUDES."html_buttons_include.php";
require_once INCLUDES."infusions_include.php";
require_once INFUSIONS."figurelib/_functions.inc.php";

// Correct Language
if (file_exists(INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php")) {
    include INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php";
} else {
    include INFUSIONS."figurelib/locale/English/locale_figurelib.php";
}

// Sitetitle and Page Container
add_to_title($locale['global_200'].$locale['figure_521']);
openside($locale['figure_521']);

// Check if there can Members submit
if (iMEMBER && $figurelibSettings['figure_submit']) {
	
	// Check if there exists Cats
	if (dbcount("(figure_cat_id)", DB_FIGURE_CATS, multilang_table("FI") ? "figure_cat_language='".LANGUAGE."'" : "")) {
	
		// Get Empty Figure Datas
		$data = [
			"figure_freigabe"     => 0,
			"figure_agb"          => 0,							
			"figure_language"     => LANGUAGE,
			"figure_title"        => "",
			"figure_variant"      => "",
			"figure_manufacturer" => "",
			"figure_artists"      => "",
			"figure_country"      => "",
			"figure_brand"        => "",
			"figure_series"       => "",
			"figure_scale"        => "",
			"figure_weight"       => "",
			"figure_height"       => "",
			"figure_width"        => "",
			"figure_depth"        => "",
			"figure_material"     => "",
			"figure_poa"          => "",
			"figure_packaging"    => "",
			"figure_retailprice"  => "",
			"figure_usedprice"    => "",
			"figure_limitation"   => "",
			"figure_videourl"     => "",
			"figure_cat"          => 0,
			"figure_editionsize"  => "",
			"figure_pubdate"      => "",
			"figure_submitter"    => $userdata['user_id'],
			"figure_visibility"   => iGUEST,
			"figure_accessories"  => "",
			"figure_description"  => "",
			"figure_datestamp"    => time()
		];
		
		// Handle posted Informations
		if (isset($_POST['save_figure'])) {
			$postdata = [
				"figure_agb"            		=> isset($_POST['figure_agb']) ? "1" : "0",										
				"figure_title"         			=> form_sanitizer($_POST['figure_title'],        "", "figure_title"),
				"figure_variant"        		=> form_sanitizer($_POST['figure_variant'],      "", "figure_variant"),
				"figure_manufacturer"   		=> form_sanitizer($_POST['figure_manufacturer'], "", "figure_manufacturer"),
				"figure_artists"        		=> form_sanitizer($_POST['figure_artists'],      "", "figure_artists"),
				"figure_country"        		=> form_sanitizer($_POST['figure_country'],      "", "figure_country"),
				"figure_brand"          		=> form_sanitizer($_POST['figure_brand'],        "", "figure_brand"),
				"figure_series"         		=> form_sanitizer($_POST['figure_series'],       "", "figure_series"),
				"figure_scale"          		=> form_sanitizer($_POST['figure_scale'],        "", "figure_scale"),
				"figure_weight"         		=> form_sanitizer($_POST['figure_weight'],       "", "figure_weight"),
				"figure_height"         		=> form_sanitizer($_POST['figure_height'],       "", "figure_height"),
				"figure_width"          		=> form_sanitizer($_POST['figure_width'],        "", "figure_width"),
				"figure_depth"          		=> form_sanitizer($_POST['figure_depth'],        "", "figure_depth"),
				"figure_material"       		=> form_sanitizer($_POST['figure_material'],     "", "figure_material"),
				"figure_poa"            		=> form_sanitizer($_POST['figure_poa'],          "", "figure_poa"),
				"figure_packaging"      		=> form_sanitizer($_POST['figure_packaging'],    "", "figure_packaging"),
				"figure_retailprice"    		=> form_sanitizer($_POST['figure_retailprice'],  0,  "figure_retailprice"),
				"figure_usedprice"      		=> form_sanitizer($_POST['figure_usedprice'],    0,  "figure_usedprice"),
				"figure_limitation"     		=> form_sanitizer($_POST['figure_limitation'],   "", "figure_limitation"),
				"figure_videourl"       		=> form_sanitizer($_POST['figure_videourl'],     "", "figure_videourl"),
				"figure_cat"            		=> form_sanitizer($_POST['figure_cat'],          "", "figure_cat"),
				"figure_editionsize"    		=> (isset($_POST['figure_editionsize']) ? form_sanitizer($_POST['figure_editionsize'],  "", "figure_editionsize") : ""),
				"figure_pubdate"        		=> stripinput(trim($_POST['figure_pubdate'])),
				"figure_description"    		=> addslash(nl2br(parseubb(stripinput($_POST['figure_description'])))),
				"figure_accessories"    		=> addslash(nl2br(parseubb(stripinput($_POST['figure_accessories']))))
			];	
			$data = array_merge($data, $postdata);
			
			// Check double Entry
			if (dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_title='".$data['figure_title']."' AND figure_cat='".$data['figure_cat']."'")) {
				$defender -> setInputError("figure_title");
				$defender -> setErrorText("figure_title", "There is a submitted Figure with this Name in the same Category!");
				defender::stop();
			}
			
			// Check AGBs
			if (!$data['figure_agb']) {
				global $defender;
				$defender->setInputError("figure_agb");
				defender::stop();
			}
			
			// Insert Figure data 
			if (defender::safe()) {
				dbquery_insert(DB_FIGURE_ITEMS, $data, "save", ["keep_session" => true]);
				$data['figure_id'] = dblastid();
			} else {
				$data['figure_id'] = 0;
			}
					
					
			// Images
			if ($data['figure_id']) {
				$formFailure = false;
				$upload = form_sanitizer($_FILES['figure_images'], "", "figure_images");
			
				// Handle each Image
				if (!empty($upload)) {
					foreach ($upload AS $imgData) {
						if ($imgData['error'] == "0") {
							$imgSaveData = [
								"figure_images_figure_id" => $data['figure_id'],
								"figure_images_image" => $imgData['image_name'],
								"figure_images_thumb" => $imgData['thumb1_name'],
								"figure_images_thumb2" => $imgData['thumb2_name'],
								"figure_images_language" => LANGUAGE
							];
							dbquery_insert(DB_FIGURE_IMAGES, $imgSaveData, "save", ["keep_session" => true]);
						} else {
							$formFailure = true; break;
						}
					}
				} else {
					$formFailure = true;
				}
						
		
				// Check Images
				if ($formFailure) {
					if (!dbcount("(figure_images_image_id)", DB_FIGURE_IMAGES, "figure_images_figure_id='".$data['figure_id']."'")) {
						addNotice("danger", "You must upload at least one Image to this Figure.", FUSION_SELF, false);
					}
					defender::stop();
					dbquery("DELETE FROM ".DB_FIGURE_ITEMS." WHERE figure_id='".$data['figure_id']."'");
					figures_deleteImages("figures", $data['figure_id']);
					
				// Otherwise do Notification
				} else {	
				
					// Send Notification
					if ($figurelibSettings['figure_notification'] != "1") {
						$figurelibSettings['figure_notification_message'] = str_replace("{FIGURELINK}", $settings['siteurl']."infusions/figurelib/figures.php?figure_id=".$unique_id."&action=admincatcher", $figurelibSettings['figure_notification_message']);
						
						// Send Message Notification
						if ($figurelibSettings['figure_notification'] == "2") {
							require_once INCLUDES."infusions_include.php";
							send_pm("-103", $userdata['user_id'], $figurelibSettings['figure_notification_subject'], $figurelibSettings['figure_notification_message'], "y", true);
							
						// Send Mail Notification
						} elseif ($figurelibSettings['figure_notification'] == "3") {
							$resultAdmins = dbquery("SELECT user_name, user_email FROM ".DB_USERS." WHERE user_id != '".$userdata['user_id']."' AND user_level='-103' AND user_status='0'");
							if (dbrows($resultAdmins)) {
								require_once INCLUDES."sendmail_include.php";
								$figurelibSettings['figure_notification_message'] = nl2br(parseubb(parsesmileys($figurelibSettings['figure_notification_message'])));
								while ($dataAdmin = dbarray($resultAdmins)) {
									sendemail($dataAdmin['user_name'], $dataAdmin['user_email'], $userdata['user_name'], $userdata['user_email'], $figurelibSettings['figure_notification_subject'], $figurelibSettings['figure_notification_message'], "html");
								}
							}
						}
					}
				}
			}
			
			// If Save was successfully.
			if (defender::safe()) {
				addNotice("success", $locale['figs_0018']);
				redirect(clean_request("submitted=f", array("stype"), true));
			}
		}	
	
		// Display success Message
		if (isset($_GET['submitted']) && $_GET['submitted'] == "f") {
		    echo "<div class='well text-center'>\n";
				echo "<p><strong>".$locale['figs_0018']."</strong></p>";
				echo "<p><a href='".INFUSIONS."figurelib/submit.php?stype=f'>".$locale['figs_0019']."</a></p>";
				echo "<p><a href='".BASEDIR."home.php'>".str_replace("[SITENAME]", fusion_get_settings("sitename"), $locale['figs_0020'])."</a></p>\n";
			echo "</div>\n";
			
		// Display Info Message with Form
        } else {
			echo "<div class='panel panel-default tbl-border'>\n<div class='panel-body'>\n";
				echo "<div class='alert alert-info m-b-20 submission-guidelines'>".str_replace("[SITENAME]", fusion_get_settings("sitename"),$locale['figure_532'])."</div>\n";

				
				// Display Divider
				echo "<div class='tbl1'><hr /></div>\n";		
						
						
				// Start Form
				echo openform("inputform", "post", FUSION_REQUEST, array("class" => "m-t-20", "enctype" => true));

				// Formfield "Figure Title"
				echo form_text("figure_title", $locale['figurelib/admin/figurelib.php_004'], $data['figure_title'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_005'],
					"error_text"  => $locale['figurelib/admin/figurelib.php_006'],
					"inline"      => true,
					"width"		  => "100%",
					"required"    => true
				]);
				
				// Formfield "Figure Category"
				echo form_select_tree("figure_cat", $locale['figurelib/admin/figurelib.php_007'], $data['figure_cat'], [
					"width" => "400px",
					"placeholder" => $locale['figurelib/admin/figurelib.php_008'],
					"error_text"  => $locale['figurelib/admin/figurelib.php_070'],
					"inline"      => true,
					"required"    => true,
					"width"		  => "100%",
					"no_root"     => 1,
					"query"       => (multilang_table("FI") ? "WHERE figure_cat_language='".LANGUAGE."'" : ""),
					"maxselect"   => 1,
					"allowclear"  => true
				], DB_FIGURE_CATS, "figure_cat_name", "figure_cat_id", "figure_cat_parent");
				
				// Formfield "Figure Variant"
				echo form_text("figure_variant", $locale['figurelib/admin/figurelib.php_010'], $data['figure_variant'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_011'],
					"width"		  => "100%",
					"inline" => true
				]);
				
				// Formfield "Figure Manufacturer"
				echo form_select_tree("figure_manufacturer", $locale['figurelib/admin/figurelib.php_012'], $data['figure_manufacturer'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_013'],
					"error_text"  => $locale['figurelib/admin/figurelib.php_014'],
					"inline"      => true,
					"width"		  => "100%",
					"required"    => true,
					"no_root"     => 1,
					"query"       => (multilang_table("FI") ? "WHERE figure_manufacturer_language='".LANGUAGE."'" : ""),
					"maxselect"   => 1,
					"allowclear"  => true
				], DB_FIGURE_MANUFACTURERS, "figure_manufacturer_name", "figure_manufacturer_id", "figure_manufacturer_parent");
				
				// Formfield "Figure Artists"
				echo form_text("figure_artists", $locale['figurelib/admin/figurelib.php_015'], $data['figure_artists'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_016'],
					"width"		  => "100%",
					"inline" => true
				]);
				
				// Formfield "Figure Country"
				$countries = array();
				require_once INCLUDES."geomap/geomap.inc.php";
				$countries = array_merge(["Unknown"], $countries);
				echo form_select("figure_country", $locale['figurelib/admin/figurelib.php_017'], $data['figure_country'], [
					"options" => $countries,
					"width"		  => "100%",
					"placeholder" => $locale['figurelib/admin/figurelib.php_018'],
					"inline" => true,
					"allowclear" => true
				]);

				// Formfield "Figure Brand"
				echo form_select_tree("figure_brand", $locale['figurelib/admin/figurelib.php_019'], $data['figure_brand'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_020'],
					"error_text"  => $locale['figurelib/admin/figurelib.php_021'],
					"width"		  => "100%",
					"inline"      => true,
					"required"    => true,
					"no_root"     => 1,
					"query"       => (multilang_table("FI") ? "WHERE figure_brand_language='".LANGUAGE."'" : ""),
					"maxselect"   => 1,
					"allowclear"  => true
				], DB_FIGURE_BRANDS, "figure_brand_name", "figure_brand_id", "figure_brand_parent");
				
				// Formfield "Figure Series"
				echo form_text("figure_series", $locale['figure_439'], $data['figure_series'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_023'],
					"width"		  => "100%",
					"inline" => true
				]);
				
				
				// Display Divider
				echo "<div class='tbl1'><hr /></div>\n";
				
				
				// Formfield "Figure Scale"
				echo form_select_tree("figure_scale", $locale['figurelib/admin/figurelib.php_024'], $data['figure_scale'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_025'],
					"error_text"  => $locale['figurelib/admin/figurelib.php_026'],
					"width"		  => "100%",
					"inline"      => true,
					"required"    => true,
					"no_root"     => 1,
					"query"       => (multilang_table("FI") ? "WHERE figure_scale_language='".LANGUAGE."'" : ""),
					"maxselect"   => 1,
					"allowclear"  => true
				], DB_FIGURE_SCALES, "figure_scale_name", "figure_scale_id", "figure_scale_parent");

				// Formfield "Figure Weight"
				echo form_text("figure_weight", $locale['figurelib/admin/figurelib.php_027'], $data['figure_weight'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_028'],
					"width"		  => "100%",
					"inline" => true
				]);

				// Formfield "Figure Height"
				echo form_select_tree("figure_height", $locale['figurelib/admin/figurelib.php_029'], $data['figure_height'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_030'],
					"error_text"  => $locale['figurelib/admin/figurelib.php_031'],
					"width"		  => "100%",
					"inline"      => true,
					"required"    => true,
					"no_root"     => 1,
					"query"       => (multilang_table("FI") ? "WHERE figure_measurements_language='".LANGUAGE."'" : ""),
					"maxselect"   => 1,
					"allowclear"  => true
				], DB_FIGURE_MEASUREMENTS, "figure_measurements_inch", "figure_measurements_id", "figure_measurements_parent");
				
				// Formfield "Figure Width"
				echo form_select_tree("figure_width", $locale['figurelib/admin/figurelib.php_032'], $data['figure_width'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_033'],
					"error_text"  => $locale['figurelib/admin/figurelib.php_034'],
					"width"		  => "100%",
					"inline"      => true,
					"required"    => true,
					"no_root"     => 1,
					"query"       => (multilang_table("FI") ? "WHERE figure_measurements_language='".LANGUAGE."'" : ""),
					"maxselect"   => 1,
					"allowclear"  => true
				], DB_FIGURE_MEASUREMENTS, "figure_measurements_inch", "figure_measurements_id", "figure_measurements_parent");
				
				// Formfield "Figure Depth"
				echo form_select_tree("figure_depth", $locale['figurelib/admin/figurelib.php_035'], $data['figure_depth'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_036'],
					"error_text"  => $locale['figurelib/admin/figurelib.php_037'],
					"width"		  => "100%",
					"inline"      => true,
					"required"    => true,
					"no_root"     => 1,
					"query"       => (multilang_table("FI") ? "WHERE figure_measurements_language='".LANGUAGE."'" : ""),
					"maxselect"   => 1,
					"allowclear"  => true
				], DB_FIGURE_MEASUREMENTS, "figure_measurements_inch", "figure_measurements_id", "figure_measurements_parent");
				
				
				// Display Divider
				echo "<div class='tbl1'><hr /></div>\n";
				
				
				// Formfield "Figure Material"
				echo form_select_tree("figure_material", $locale['figurelib/admin/figurelib.php_038'], $data['figure_material'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_039'],
					"error_text"  => $locale['figurelib/admin/figurelib.php_040'],
					"width"		  => "100%",
					"inline"      => true,
					"required"    => true,
					"no_root"     => 1,
					"query"       => (multilang_table("FI") ? "WHERE figure_material_language='".LANGUAGE."'" : ""),
					"maxselect"   => 1,
					"allowclear"  => true
				], DB_FIGURE_MATERIALS, "figure_material_name", "figure_material_id", "figure_material_parent");
				
				// Formfield "Figure POA"
				echo form_select_tree("figure_poa", $locale['figurelib/admin/figurelib.php_041'], $data['figure_poa'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_042'],
					"error_text"  => $locale['figurelib/admin/figurelib.php_043'],
					"width"		  => "100%",
					"inline"      => true,
					"required"    => true,
					"no_root"     => 1,
					"query"       => (multilang_table("FI") ? "WHERE figure_poa_language='".LANGUAGE."'" : ""),
					"maxselect"   => 1,
					"allowclear"  => true
				], DB_FIGURE_POAS, "figure_poa_name", "figure_poa_id", "figure_poa_parent");
				
				// Formfield "Figure Packagings"
				echo form_select_tree("figure_packaging", $locale['figurelib/admin/figurelib.php_044'], $data['figure_packaging'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_045'],
					"error_text"  => $locale['figurelib/admin/figurelib.php_046'],
					"width"		  => "100%",
					"inline"      => true,
					"required"    => true,
					"no_root"     => 1,
					"query"       => (multilang_table("FI") ? "WHERE figure_packaging_language='".LANGUAGE."'" : ""),
					"maxselect"   => 1,
					"allowclear"  => true
				], DB_FIGURE_PACKAGINGS, "figure_packaging_name", "figure_packaging_id", "figure_packaging_parent");
				
				
				// Display Divider
				echo "<div class='tbl1'><hr /></div>\n";
				
				
				// Formfield "Figure Pubdate"
				echo "<div id='figure_pubdate-field' class='form-group'>\n";
					echo "<label class='control-label col-xs-12 col-sm-3 col-md-3 col-lg-3 p-l-0' for='figure_pubdate'>". $locale['figurelib/admin/figurelib.php_047']."</label>\n";
					echo "<div class='col-xs-12 col-sm-9 col-md-9 col-lg-9'>\n";
						echo "<div class='input-group date'>\n";
							echo "<input type='text' name='figure_pubdate' id='figure_pubdate' value='".$data['figure_pubdate']."' class='form-control textbox' placeholder='".$locale['figurelib/admin/figurelib.php_048']."' />\n";
							echo "<span class='input-group-addon'><i class='entypo calendar'></i></span>\n";
						echo "</div>\n";
					echo "</div>\n";
				echo "</div>\n";

				// Formfield "Figure Retailprice"
				echo form_text("figure_retailprice", $locale['figurelib/admin/figurelib.php_050'], $data['figure_retailprice'], [
					"placeholder"   => $locale['figurelib/admin/figurelib.php_051'],
					"width"		    => "100%",
					"inline"        => true,
					"min"           => 0,
					"prepend_value" => "&#036;",
					"type"          => "number"
				]);
				
				// Formfield "Figure Usedprice"
				echo form_text("figure_usedprice", $locale['figurelib/admin/figurelib.php_052'], $data['figure_usedprice'], [
					"placeholder"   => $locale['figurelib/admin/figurelib.php_053'],
					"width"		    => "100%",
					"inline"        => true,
					"min"           => 0,
					"prepend_value" => "&#036;",
					"type"          => "number"
				]);
				
				
				// Display Divider
				echo "<div class='tbl1'><hr /></div>\n";
				
				
				// Formfield "Figure Limited Edition"
				echo form_select_tree("figure_limitation", $locale['figurelib/admin/figurelib.php_054'], $data['figure_limitation'], [
					"placeholder" => $locale['figurelib/admin/figurelib.php_055'],
					"error_text"  => $locale['figurelib/admin/figurelib.php_056'],
					"width"		  => "100%",
					"inline"      => true,
					"required"    => true,
					"no_root"     => 1,
					"query"       => (multilang_table("FI") ? "WHERE figure_limitation_language='".LANGUAGE."'" : ""),
					"maxselect"   => 1,
					"allowclear"  => true
				], DB_FIGURE_LIMITATIONS, "figure_limitation_name", "figure_limitation_id", "figure_limitation_parent");

				// Formfield "Figure Editions Size"
				echo form_text("figure_editionsize", $locale['figurelib/admin/figurelib.php_057'], $data['figure_editionsize'], [
					"placeholder"   => $locale['figurelib/admin/figurelib.php_058'],
					"width"		    => "100%",
					"inline"        => true,
					"min"           => 0,
					"type"          => "number"
				]);
				
				// Display Divider
				echo "<div class='tbl1'><hr /></div>\n";
								
				// Formfield "Figure Video Url"
				echo form_text("figure_videourl", $locale['figure_461'], $data['figure_videourl'], [
					"placeholder"   => $locale['figure_1818'],
					"error_text"    => $locale['figurelib-error-113'],
					"width"		    => "100%",
					"inline"        => true,
					"max_lenght"    => 40
				]);
				
				// Diplay Video	
				echo "<div class='row'>\n";	
					echo "<div class='col-lg-3 col-md-3 col-sm-12 col-xs-12'>\n";
						echo "<strong>Video Preview</strong>\n";
					echo "</div>\n";									
					echo "<div class='col-lg-9 col-md-9 col-sm-12 col-xs-12'>\n";
						echo "<div id='figure_videopreview'></div>\n";
					echo "</div>\n";
				echo "</div>\n";	
				
				
				// Display Divider
				echo "<div class='tbl1'><hr /></div>\n";

				// Formfield "Figure Images"
				echo "<div class='well'>\n";
				echo form_fileinput("figure_images[]", $locale['figurelib/admin/figurelib.php_059'], "", [
					"inline"            => true,
					"ext_tip"           => sprintf($locale['figure_035'], parsebytesize($figurelibSettings['figure_photo_max_b']).$locale['figure_036'].$figurelibSettings['figure_image_upload_count']),
					"deactivate"        => "",
					"multiple"          => true,
					"template"          => "modern",
					"delete_original"   => false,
					"upload_path"       => FIGURES,
					"max_count"         => $figurelibSettings['figure_image_upload_count'],
					"max_byte"          => $figurelibSettings['figure_photo_max_b'],
					"max_width"         => $figurelibSettings['figure_photo_max_w'],
					"max_height"        => $figurelibSettings['figure_photo_max_h'],
					"thumbnail"         => true,
					"thumbnail_w"       => $figurelibSettings['figure_thumb_w'],
					"thumbnail_h"       => $figurelibSettings['figure_thumb_h'],
					"thumbnail_suffix"  => "_t1",
					"thumbnail_folder"  => "thumbs/",
					"thumbnail2"        => true,
					"thumbnail2_w"      => $figurelibSettings['figure_thumb2_w'],
					"thumbnail2_h"      => $figurelibSettings['figure_thumb2_h'],
					"thumbnail2_suffix" => "_t2",
				]);
				echo "</div>\n";
				
				
				// Display Divider
				echo "<div class='tbl1'><hr /></div>\n";
				
				
				// Formfield "Accessories"
				echo form_textarea("figure_accessories", $locale['figurelib/admin/figurelib.php_060'], $data['figure_accessories'], [
					"type"      => fusion_get_settings("tinymce_enabled") ? "tinymce" : "html",
					"tinymce"   => fusion_get_settings("tinymce_enabled") && iADMIN ? "advanced" : "simple",
					#"autosize"  => true,
					"required"  => false,
					"form_name" => "inputform"
				]);
				
				// Formfield "Description"
				echo form_textarea("figure_description", $locale['figurelib/admin/figurelib.php_061'], $data['figure_description'], [
					"type"      => fusion_get_settings("tinymce_enabled") ? "tinymce" : "html",
					"tinymce"   => fusion_get_settings("tinymce_enabled") && iADMIN ? "advanced" : "simple",
					#"autosize"  => true,
					"required"  => false,
					"form_name" => "inputform"
				]);
				
				
				// Display Divider
				echo "<div class='tbl1'><hr /></div>\n";
				
				
				// Formfield "Terms"
				echo form_checkbox("figure_agb", $locale['figurelib/admin/figurelib.php_062'], $data['figure_agb'], ["reverse_label" => true, "required" => true, "error_text" => $locale['figurelib/admin/figurelib.php_063']]);
				
				
				// Display Divider
				echo "<div class='tbl1'><hr /></div>\n";
				
				
				// Save Button
				echo form_button("save_figure", $locale['figure_521'], $locale['figure_521'], ["class" => "btn btn-primary"]);

				// Close Form
				echo closeform();
							
			echo "</div>\n</div>\n";
		}
		
	// If there are no Categories, display a Message
	} else {
		echo "<div class='well text-center'>\n".$locale['figure_1812']."<br />\n".$locale['figurelib/admin/figurelib.php_066']."</div>\n";
	}
	
// If there are no Members submit, display a Message
} else {
	echo "<div class='well text-center'>".$locale['figure_1813']."</div>\n";
}

// Close Container
closeside();

// Datepicker include
if (!defined('DATEPICKER')) {
	define('DATEPICKER', TRUE);
    add_to_head("<link href='".DYNAMICS."assets/datepicker/css/datetimepicker.min.css' rel='stylesheet' />");
    add_to_footer("<script src='".DYNAMICS."assets/datepicker/js/moment.min.js'></script>");
    add_to_footer("<script src='".DYNAMICS."assets/datepicker/js/datetimepicker.min.js'></script>");
    add_to_head("<script src='".DYNAMICS."assets/datepicker/locale/".$locale['datepicker'].".js'></script>");
}
	
// Add jQuery
add_to_footer("
<script type='text/javascript'>
$(function() {
	$('input[name=\"figure_videourl\"]').click(function(){
		return confirm('Please note, that all Videos must be Copyright Free!');
	});

	$('#figure_videourl').blur(function(){
		var embed = $('#figure_videourl').val();
		if (embed) {
			$('#figure_videopreview').html('<div class=\"embed-responsive embed-responsive-16by9\"><iframe class=\"embed-responsive-item\" src=\"https://www.youtube.com/embed/' + embed + '\"></iframe></div>');
		} else {
			$('#figure_videopreview').html('<div>Sorry! There is no Video avaible to this Figure.</div>');
		}
    });
	
	$('#figure_videourl').ready(function(){
		var embed = $('#figure_videourl').val();
		if (embed) {
			$('#figure_videopreview').html('<div class=\"embed-responsive embed-responsive-16by9\"><iframe class=\"embed-responsive-item\" src=\"https://www.youtube.com/embed/' + embed + '\"></iframe></div>');
		} else {
			$('#figure_videopreview').html('<div>Sorry! There is no Video avaible to this Figure.</div>');
		}
    });
});

$(function() {
	$('#figure_limitation').ready(function(){
		var opt = $('#figure_limitation').val();
        if (opt != 2) {
			$('#figure_editionsize').val('');
            $('#figure_editionsize').prop('disabled', true);
		}
    });
});

$(function(){
    $('#figure_limitation').change(function(){
        if ($(this).val() != 2) {
            $('#figure_editionsize').val('');
            $('#figure_editionsize').prop('disabled', true);
        } else {
            $('#figure_editionsize').prop('disabled', false);
        }
    });
});

$('#figure_pubdate-field .input-group.date').datetimepicker({
		showTodayButton: true,
		showClear: true,
		showClose: true,
		allowInputToggle: true,
		format: 'YYYY / MMMM',
});

$(function() {
    $('input[name=\"figure_images[]\"]').click(function(){
        return confirm('Please note, that all uploaded Images must be Copyright Free!');
    });
});
</script>");

// Systemfile
require_once THEMES."templates/footer.php";

?>