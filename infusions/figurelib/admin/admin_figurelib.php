<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: admin_figurelib.php based on admin/weblink.php
| Author: PHP-Fusion Development Team
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
if (!defined("IN_FUSION")) {
    die("Access Denied");
}

pageAccess("FI");
global $figurelibSettings;
$formEdit = false;

// LANGUAGE
if (file_exists(INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php")) {
    include INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php";
} else {
    include INFUSIONS."figurelib/locale/English/locale_figurelib.php";
}

if (file_exists(LOCALE.LOCALESET."admin/settings.php")) {
    include LOCALE.LOCALESET."admin/settings.php";
} else {
    include LOCALE."English/admin/settings.php";
}

// Check if there exists Categories
$result = dbcount("(figure_cat_id)", DB_FIGURE_CATS);
if (!empty($result)) {
	
	// Delete a Figure with Images
	if (
		(isset($_GET['action']) && $_GET['action'] == "delete") && 
		(isset($_GET['figure_id']) && isnum($_GET['figure_id']) && dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_id='".$_GET['figure_id']."'"))
	) {
		
		// Check if no Users have this Figure in there Collection
		if (!dbcount("('figure_userfigures_figure_id')", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".intval($_GET['figure_id'])."'")) {

			// Delete Figure
			figures_deleteImages("figures", $_GET['figure_id']);
			dbquery("DELETE FROM ".DB_FIGURE_ITEMS." WHERE figure_id='".$_GET['figure_id']."'");
			dbquery("DELETE FROM ".DB_COMMENTS." WHERE comment_type='FI' AND comment_item_id='".$_GET['figure_id']."'");
			dbquery("DELETE FROM ".DB_RATINGS." WHERE rating_type='FI' AND rating_item_id='".$_GET['figure_id']."'");
			addNotice('success', "Figure deleted");
		
		// Otherwise display a Message
		} else {
			addNotice("danger", "Cannot delete figure because some user still has it");
		}
	
	// Add / Edit a Figure
	} else {
		
		// Get Empty Figure Datas
		$data = [
			"figure_id"						=> 0,
			"figure_freigabe"       		=> 0,
			"figure_agb"           			=> 0,					
			"figure_show_images"      		=> 1,
			"figure_show_data"          	=> 1,
			"figure_show_videos"       		=> 1,
			"figure_show_affiliates"   		=> 1,
			"figure_show_amazon"        	=> 1,
			"figure_show_ebay"         		=> 1,
			"figure_show_related"      		=> 1,			
			"figure_show_collection"     	=> 1,
			"figure_show_comments"     		=> 1,
			"figure_show_ratings"       	=> 1,
			"figure_show_social_sharing" 	=> 1,					
			"figure_language"       		=> LANGUAGE,
			"figure_title"          		=> "",
			"figure_variant"        		=> "",
			"figure_manufacturer"   		=> "",
			"figure_artists"        		=> "",
			"figure_country"        		=> "",
			"figure_brand"          		=> "",
			"figure_series"         		=> "",
			"figure_scale"          		=> "",
			"figure_weight"         		=> "",
			"figure_height"         		=> "",
			"figure_width"          		=> "",
			"figure_depth"          		=> "",
			"figure_material"       		=> "",
			"figure_poa"            		=> "",
			"figure_packaging"      		=> "",
			"figure_retailprice"    		=> "",
			"figure_usedprice"      		=> "",
			"figure_limitation"     		=> "",
			"figure_videourl"       		=> "",
			"figure_cat"            		=> 0,
			"figure_editionsize"    		=> "",
			"figure_pubdate"        		=> "",
			"figure_submitter"      		=> $userdata['user_id'],
			"figure_visibility"     		=> iGUEST,
			"figure_forum_url"      		=> "",
			"figure_affiliate_1"    		=> "",
			"figure_affiliate_2"    		=> "",
			"figure_affiliate_3"    		=> "",
			"figure_affiliate_4"    		=> "",
			"figure_affiliate_5"    		=> "",
			"figure_affiliate_6"    		=> "",
			"figure_affiliate_7"    		=> "",
			"figure_affiliate_8"    		=> "",
			"figure_affiliate_9"    		=> "",
			"figure_affiliate_10"   		=> "",
			"figure_eshop"          		=> "",
			"figure_amazon_de"      		=> "",
			"figure_amazon_uk"      		=> "",
			"figure_amazon_fr"      		=> "",
			"figure_amazon_es"      		=> "",
			"figure_amazon_it"      		=> "",
			"figure_amazon_jp"      		=> "",
			"figure_amazon_com"     		=> "",
			"figure_amazon_ca"      		=> "",
			"figure_accessories"    		=> "",
			"figure_description"    		=> "",
			"figure_datestamp"      		=> time()
		];
		
		// Edit a Figure?
		if (
			(isset($_GET['action']) && $_GET['action'] == "edit") && 
			(isset($_GET['figure_id']) && isnum($_GET['figure_id'])) && 
			dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_id='".$_GET['figure_id']."'")
		) {
			$data = dbarray(dbquery("
				SELECT 
					*
				FROM ".DB_FIGURE_ITEMS."
				WHERE figure_id='".$_GET['figure_id']."'
				LIMIT 0,1
			"));
			$formEdit = true;
		}

		// Handle posted Informations
		if (isset($_POST['save_figure'])) {
			$data = [
				"figure_freigabe"       		=> isset($_POST['figure_freigabe'])       ? "1" : "0",
				"figure_agb"            		=> isset($_POST['figure_agb'])            ? "1" : "0",								
				"figure_show_images"      		=> form_sanitizer($_POST['figure_show_images'],     	"", "figure_show_images"),
				"figure_show_data"        		=> form_sanitizer($_POST['figure_show_data'],     		"", "figure_show_data"),
				"figure_show_videos"      		=> form_sanitizer($_POST['figure_show_videos'],     	"", "figure_show_videos"),
				"figure_show_affiliates"   		=> form_sanitizer($_POST['figure_show_affiliates'],     "", "figure_show_affiliates"),
				"figure_show_amazon"      		=> form_sanitizer($_POST['figure_show_amazon'],     	"", "figure_show_amazon"),
				"figure_show_ebay"        		=> form_sanitizer($_POST['figure_show_ebay'],     		"", "figure_show_ebay"),				
				"figure_show_related"       	=> form_sanitizer($_POST['figure_show_related'],     	"", "figure_show_related"),
				"figure_show_collection"       	=> form_sanitizer($_POST['figure_show_collection'],     "", "figure_show_collection"),
				"figure_show_comments"   		=> form_sanitizer($_POST['figure_show_comments'],     	"", "figure_show_comments"),
				"figure_show_ratings"    		=> form_sanitizer($_POST['figure_show_ratings'],     	"", "figure_show_ratings"),
				"figure_show_social_sharing"	=> form_sanitizer($_POST['figure_show_social_sharing'], "", "figure_show_social_sharing"),						
				"figure_id"             		=> form_sanitizer($_POST['figure_id'],           0,  "figure_id"),
				"figure_language"       		=> form_sanitizer($_POST['figure_language'],     "", "figure_language"),
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
				"figure_submitter"      		=> form_sanitizer($_POST['figure_submitter'],    "", "figure_submitter"),
				"figure_visibility"     		=> form_sanitizer($_POST['figure_visibility'],   0,  "figure_visibility"),
				"figure_forum_url"      		=> form_sanitizer($_POST['figure_forum_url'],    "", "figure_forum_url"),
				"figure_affiliate_1"    		=> form_sanitizer($_POST['figure_affiliate_1'],  "", "figure_affiliate_1"),
				"figure_affiliate_2"    		=> form_sanitizer($_POST['figure_affiliate_2'],  "", "figure_affiliate_2"),
				"figure_affiliate_3"    		=> form_sanitizer($_POST['figure_affiliate_3'],  "", "figure_affiliate_3"),
				"figure_affiliate_4"    		=> form_sanitizer($_POST['figure_affiliate_4'],  "", "figure_affiliate_4"),
				"figure_affiliate_5"    		=> form_sanitizer($_POST['figure_affiliate_5'],  "", "figure_affiliate_5"),
				"figure_affiliate_6"    		=> form_sanitizer($_POST['figure_affiliate_6'],  "", "figure_affiliate_6"),
				"figure_affiliate_7"    		=> form_sanitizer($_POST['figure_affiliate_7'],  "", "figure_affiliate_7"),
				"figure_affiliate_8"    		=> form_sanitizer($_POST['figure_affiliate_8'],  "", "figure_affiliate_8"),
				"figure_affiliate_9"    		=> form_sanitizer($_POST['figure_affiliate_9'],  "", "figure_affiliate_9"),
				"figure_affiliate_10"   		=> form_sanitizer($_POST['figure_affiliate_10'], "", "figure_affiliate_10"),
				"figure_eshop"          		=> form_sanitizer($_POST['figure_eshop'],        "", "figure_eshop"),
				"figure_amazon_de"      		=> form_sanitizer($_POST['figure_amazon_de'],    "", "figure_amazon_de"),
				"figure_amazon_uk"      		=> form_sanitizer($_POST['figure_amazon_uk'],    "", "figure_amazon_uk"),
				"figure_amazon_fr"      		=> form_sanitizer($_POST['figure_amazon_fr'],    "", "figure_amazon_fr"),
				"figure_amazon_es"      		=> form_sanitizer($_POST['figure_amazon_es'],    "", "figure_amazon_es"),
				"figure_amazon_it"      		=> form_sanitizer($_POST['figure_amazon_it'],    "", "figure_amazon_it"),
				"figure_amazon_jp"      		=> form_sanitizer($_POST['figure_amazon_jp'],    "", "figure_amazon_jp"),
				"figure_amazon_com"     		=> form_sanitizer($_POST['figure_amazon_com'],   "", "figure_amazon_com"),
				"figure_amazon_ca"      		=> form_sanitizer($_POST['figure_amazon_ca'],    "", "figure_amazon_ca"),
				"figure_datestamp"      		=> form_sanitizer($_POST['figure_datestamp'],    "", "figure_datestamp"),
				"figure_description"    		=> addslash(nl2br(parseubb(stripinput($_POST['figure_description'])))),
				"figure_accessories"    		=> addslash(nl2br(parseubb(stripinput($_POST['figure_accessories']))))
			];
			
			//print_r($_POST); CHECK WAS BEI POST RAUSKOMMT

			// Check AGBs
			if (!$data['figure_agb']) {
				global $defender;
				$defender->setInputError("figure_agb");
				defender::stop();
			}

			// Delete a Image
			if ($formEdit && isset($_POST['delete_image'])) {
				if (is_array($_POST['delete_image']) && count($_POST['delete_image'])) {
					foreach($_POST['delete_image'] AS $deleteImageID) {
						if (isNum($deleteImageID)) {
							figures_deleteImage($deleteImageID);
						}
					}
				}
			}
			$formFailure = false;

			// Handle Action
			if (defender::safe()) {
				
				// Update a Figure
				if ($data['figure_id'] && $formEdit) {
					dbquery_insert(DB_FIGURE_ITEMS, $data, "update", ["keep_session" => true]);
					$message = "Figure Updated";
					
				// Save a Figure 
				} else {
					dbquery_insert(DB_FIGURE_ITEMS, $data, "save", ["keep_session" => true]);
					$message = "Figure added";
					$data['figure_id'] = dblastid();
				}
			}

			// Images
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
					
			// If its just a edit.
			if (
				$formFailure && 
				$data['figure_id'] && 
				!dbcount("(figure_images_image_id)", DB_FIGURE_IMAGES, "figure_images_figure_id='".$data['figure_id']."'") && 
				$formEdit 
			) {
				addNotice("danger", "You must at least upload one Image before you can save this Figure!", FUSION_SELF, false);
				defender::stop();
				
			// Otherwise if it is a new Figure
			} elseif ($formFailure && $data['figure_id'] && !$formEdit) {
				echo dbcount("(figure_images_image_id)", DB_FIGURE_IMAGES, "figure_images_figure_id='".$data['figure_id']."'")."<br />".$data['figure_id'];
				if (!dbcount("(figure_images_image_id)", DB_FIGURE_IMAGES, "figure_images_figure_id='".$data['figure_id']."'")) {
					addNotice("danger", "Please add at least one Image and submit the Figure again!", FUSION_SELF, false);
				}
				defender::stop();
				dbquery("DELETE FROM ".DB_FIGURE_ITEMS." WHERE figure_id='".$data['figure_id']."'");
				figures_deleteImages("figures", $data['figure_id']);
			}
		
			// If Save was successfully.
			if (defender::safe()) {
				addNotice("success", $message);
				redirect(FUSION_SELF.$aidlink);
			}
		}
	}
    
	
	// Start Form
    echo openform("inputform", "post", FUSION_REQUEST, array("class" => "m-t-20", "enctype" => true));
    echo "<div class='text-right m-b-15'>\n";
	
	// Save Button
	echo form_button("save_figure", $locale['figurelib/admin/figurelib.php_064'], $locale['figurelib/admin/figurelib.php_064'], ["class" => "btn btn-default"]);

	// Delete Button
	if (isset($_GET['action']) && $_GET['action'] == "edit") {		
		echo "<a class='btn btn-default' href='".FUSION_SELF.$aidlink."&amp;section=figurelib_form&amp;action=delete&amp;figure_id=".$data['figure_id']."' onclick=\"return confirm('".$locale['film_0004']."');\"> ".$locale['cifg_0006']."</a>"; 
	}
	echo "</div>\n";

	// Hidden Formfields
	echo form_hidden("figure_id",        "", $data['figure_id']);
    echo form_hidden("figure_datestamp", "", $data['figure_datestamp']);
    echo form_hidden("figure_submitter", "", $data['figure_submitter']);
	
/*#################################################################################################*/
	// Visibility / Publishing
    openside("<strong>VISIBILITY / PUBLISHING</strong>");
	echo "<div class='row'>\n";
	echo "<div class='col-md-12 col-xs-12'>\n";

		// Formfield "Visibility"
		echo form_select("figure_visibility", $locale['figurelib/admin/figurelib.php_009'], $data['figure_visibility'], ["inline" => true, "options" => fusion_get_groups()]);

		// Formfield "Figure Language"
		if (multilang_table("FI")) {
			echo form_select("figure_language", $locale['global_ML100'], $data['figure_language'], ["inline" => true, "options" => fusion_get_enabled_languages()]);
		} else {
			echo form_hidden("figure_language", "", $data['figure_language']);
		}
		
		// Formfield "Figure Freigabe"
			//echo form_checkbox("figure_freigabe", $locale['figurelib/admin/figurelib.php_069'], $data['figure_freigabe'], ["reverse_label" => true]);
			  echo form_select("figure_freigabe", $locale['figurelib/admin/figurelib.php_069'], $data['figure_freigabe'], array(
								"inline" => TRUE, 
								"options" => array($locale['disable'], $locale['enable'])
						));
	echo "</div>\n";
	echo "</div>\n";	
		
    closeside();	

/*#################################################################################################*/	
	
	openside("<strong>MODULE ON/OFF - FIGURE ONLY</strong>");

	// Display LEFT Column BEGIN
		echo "<div class='row'>\n";
		echo "<div class='col-md-4 col-xs-12'>\n";
		
		/*----------------------------------------------------------------------------------
		/         1					|             2				|             3				|			
		/*----------------------------------------------------------------------------------
		| figure_show_images		|							|							|
		| figure_show_data			|							|							|		
		| figure_show_videos		|							|							|		
		| figure_show_affiliates	|							|							|
		-----------------------------------------------------------------------------------*/
	
		// Formfield ['figure_371'] = "Images:"; --> figure_show_images
		echo form_checkbox("figure_show_images", $locale['figure_371'], $data['figure_show_images'], ["reverse_label" => true]);
		
		// Formfield ['figure_372'] = "Data:"; --> figure_show_data	
		echo form_checkbox("figure_show_data", $locale['figure_372'], $data['figure_show_data'], ["reverse_label" => true]);
		
		// Formfield ['figure_367'] = "Videos:"; --> figure_show_videos
		echo form_checkbox("figure_show_videos", $locale['figure_367'], $data['figure_show_videos'], ["reverse_label" => true]);
		
		// Formfield ['figure_368'] = "Affiliate:"; --> figure_show_affiliates
		echo form_checkbox("figure_show_affiliates", $locale['figure_368'], $data['figure_show_affiliates'], ["reverse_label" => true]);
				

		echo "</div>\n";			
		echo "<div class='col-md-4 col-xs-12'>\n";
		
		/*----------------------------------------------------------------------------------
		/         1					|             2				|             3				|			
		/*----------------------------------------------------------------------------------
		| 							|figure_show_amazon			|							|
		| 							|figure_show_ebay			|							|		
		| 							|figure_show_related		|							|		
		| 							|figure_show_collection		|							|
		-----------------------------------------------------------------------------------*/

		// Formfield ['figure_369'] = "Amazon Affiliate:"; --> figure_show_amazon
		echo form_checkbox("figure_show_amazon", $locale['figure_369'], $data['figure_show_amazon'], ["reverse_label" => true]);
		
		// Formfield ['figure_370'] = "Ebay Affiliate"; --> figure_show_ebay
		echo form_checkbox("figure_show_ebay", $locale['figure_370'], $data['figure_show_ebay'], ["reverse_label" => true]);
		
		// Formfield ['figure_348'] = "Related"; --> figure_show_related	
		echo form_checkbox("figure_show_related", $locale['figure_348'], $data['figure_show_related'], ["reverse_label" => true]);
		
		// Formfield ['figure_373'] = "Collection"; --> figure_show_collection
		echo form_checkbox("figure_show_collection", $locale['figure_373'], $data['figure_show_collection'], ["reverse_label" => true]);
	
	echo "</div>\n";
	echo "<div class='col-md-4 col-xs-12'>\n";
	
		/*----------------------------------------------------------------------------------
		/         1					|             2				|             3				|			
		/*----------------------------------------------------------------------------------
		| 							|							|figure_show_comments		|
		| 							|							|figure_show_ratings		|		
		| 							|							|figure_show_social_sharing	|		
		| 							|							|							|
		-----------------------------------------------------------------------------------*/
	
		// Formfield ['figure_363'] = "Comments"; --> figure_show_comments
		echo form_checkbox("figure_show_comments", $locale['figure_363'], $data['figure_show_comments'], ["reverse_label" => true]);
		
		// Formfield ['figure_364'] = "Ratings"; --> figure_show_ratings
		echo form_checkbox("figure_show_ratings", $locale['figure_364'], $data['figure_show_ratings'], ["reverse_label" => true]);
				
		// Formfield ['figure_344'] = "Social Sharing"; --> figure_show_social_sharing
		echo form_checkbox("figure_show_social_sharing", $locale['figure_344'], $data['figure_show_social_sharing'], ["reverse_label" => true]);
	
	
	echo "</div>\n";	
	echo "</div>\n";
	// MODULE ON/OFF END
    closeside();
	
/*#################################################################################################*/	
	
	// Images
    openside("<strong>IMAGES</strong>");
	
	// Count avaible Images
	$imageCounter = ($data['figure_id'] ? dbcount("(figure_images_image_id)", DB_FIGURE_IMAGES, "figure_images_figure_id='".$data['figure_id']."'") : 0);

	// Formfield "Figure Images"
    echo "<div class='well'>\n";
    echo form_fileinput("figure_images[]", $locale['figurelib/admin/figurelib.php_059'], "", [
		"inline"            => true,
		#"required"          => ($data['figure_id'] == "0") ? true : false,
		"ext_tip"           => sprintf($locale['figure_035'], parsebytesize($figurelibSettings['figure_photo_max_b']).$locale['figure_036'].$figurelibSettings['figure_image_upload_count']),
		"deactivate"        => "",
		"multiple"          => true,
		"template"          => "modern",
		"delete_original"   => false,
		"upload_path"       => FIGURES,
		"max_count"         => ($figurelibSettings['figure_image_upload_count'] - $imageCounter + 1),
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

	// Display avaible Images
    if ($formEdit && $imageCounter) {
        echo "<div class='row'>\n";
		
			// Get all Images
			$avaibleImages = figures_getImagePath("figures", "thumb", $data['figure_id'], "all");
			
			// Check if there avaible Images
			if (is_array($avaibleImages) && count($avaibleImages)) {
				foreach ($avaibleImages AS $avaibleImage) {
					echo "<div class='col-xs-12 col-sm-3'>\n";
						echo form_checkbox("delete_image[]", "Delete this Image<br /><img src='".$avaibleImage['image']."' alt='".$avaibleImage['id']."' style='max-width: 200px;' />", "", [
							"value" => $avaibleImage['id'],
							"input_id" => "delete-image-".$avaibleImage['id'],
							"reverse_label" => true
						]);
					echo "</div>\n";
				}
			}
        echo "</div>\n";
    }
	
	// Images
    closeside();

/*#################################################################################################*/	
	
	// Basedata 
    openside("<strong>FIGURE BASEDATA</strong>");

	// Formfield "Figure Title"
    echo form_text("figure_title", $locale['figurelib/admin/figurelib.php_004'], $data['figure_title'], [
        "placeholder" => $locale['figurelib/admin/figurelib.php_005'],
        "error_text"  => $locale['figurelib/admin/figurelib.php_006'],
        "inline"      => true,
        "width"       => "520px",
        "required"    => true
    ]);
	
	// Formfield "Figure Category"
    echo form_select_tree("figure_cat", $locale['figurelib/admin/figurelib.php_007'], $data['figure_cat'], [
        "placeholder" => $locale['figurelib/admin/figurelib.php_008'],
        "error_text"  => $locale['figurelib/admin/figurelib.php_070'],
        "inline"      => true,
        "width"       => "520px",
        "required"    => true,
        "no_root"     => 1,
        "query"       => (multilang_table("FI") ? "WHERE figure_cat_language='".LANGUAGE."'" : ""),
        "maxselect"   => 1,
        "allowclear"  => true
	], DB_FIGURE_CATS, "figure_cat_name", "figure_cat_id", "figure_cat_parent");
	
	// Formfield "Figure Variant"
    echo form_text("figure_variant", $locale['figurelib/admin/figurelib.php_010'], $data['figure_variant'], [
		"placeholder" => $locale['figurelib/admin/figurelib.php_011'],
        "inline" => true,
        "width" => "520px",
    ]);
	
	// Formfield "Figure Manufacturer"
    echo form_select_tree("figure_manufacturer", $locale['figurelib/admin/figurelib.php_012'], $data['figure_manufacturer'], [
        "placeholder" => $locale['figurelib/admin/figurelib.php_013'],
        "error_text"  => $locale['figurelib/admin/figurelib.php_014'],
        "inline"      => true,
        "width"       => "520px",
        "required"    => true,
        "no_root"     => 1,
        "query"       => (multilang_table("FI") ? "WHERE figure_manufacturer_language='".LANGUAGE."'" : ""),
        "maxselect"   => 1,
        "allowclear"  => true
	], DB_FIGURE_MANUFACTURERS, "figure_manufacturer_name", "figure_manufacturer_id", "figure_manufacturer_parent");
	
	// Formfield "Figure Artists"
    echo form_text("figure_artists", $locale['figurelib/admin/figurelib.php_015'], $data['figure_artists'], [
		"placeholder" => $locale['figurelib/admin/figurelib.php_016'],
        "inline" => true,
        "width" => "520px",
    ]);
	
	// Formfield "Figure Country"
	$countries = array();
	require_once INCLUDES."geomap/geomap.inc.php";
	$countries = array_merge(["Unknown"], $countries);
	echo form_select("figure_country", $locale['figurelib/admin/figurelib.php_017'], $data['figure_country'], [
        "options" => $countries,
        "placeholder" => $locale['figurelib/admin/figurelib.php_018'],
		"inline" => true,
		"width" => "520px",
        "allowclear" => true
    ]);

	// Formfield "Figure Brand"
    echo form_select_tree("figure_brand", $locale['figurelib/admin/figurelib.php_019'], $data['figure_brand'], [
        "placeholder" => $locale['figurelib/admin/figurelib.php_020'],
        "error_text"  => $locale['figurelib/admin/figurelib.php_021'],
        "inline"      => true,
        "width"       => "520px",
        "required"    => true,
        "no_root"     => 1,
        "query"       => (multilang_table("FI") ? "WHERE figure_brand_language='".LANGUAGE."'" : ""),
        "maxselect"   => 1,
        "allowclear"  => true
	], DB_FIGURE_BRANDS, "figure_brand_name", "figure_brand_id", "figure_brand_parent");
	
	// Formfield "Figure Series"
    echo form_text("figure_series", $locale['figure_439'], $data['figure_series'], [
		"placeholder" => $locale['figurelib/admin/figurelib.php_023'],
        "inline" => true,
        "width" => "520px",
    ]);
	
	// Basedata 
    closeside();
	
/*#################################################################################################*/	
	
	// Dimsensions and More
    openside("<strong>DIMENSIONS & MORE</strong>");
	
	// Formfield "Figure Scale"
    echo form_select_tree("figure_scale", $locale['figurelib/admin/figurelib.php_024'], $data['figure_scale'], [
	    "placeholder" => $locale['figurelib/admin/figurelib.php_025'],
        "error_text"  => $locale['figurelib/admin/figurelib.php_026'],
        "inline"      => true,
        "width"       => "520px",
        "required"    => true,
        "no_root"     => 1,
        "query"       => (multilang_table("FI") ? "WHERE figure_scale_language='".LANGUAGE."'" : ""),
        "maxselect"   => 1,
        "allowclear"  => true
    ], DB_FIGURE_SCALES, "figure_scale_name", "figure_scale_id", "figure_scale_parent");

	// Formfield "Figure Weight"
    echo form_text("figure_weight", $locale['figurelib/admin/figurelib.php_027'], $data['figure_weight'], [
		"placeholder" => $locale['figurelib/admin/figurelib.php_028'],
        "inline" => true,
        "width" => "520px"
    ]);

	// Formfield "Figure Height"
    echo form_select_tree("figure_height", $locale['figurelib/admin/figurelib.php_029'], $data['figure_height'], [
	    "placeholder" => $locale['figurelib/admin/figurelib.php_030'],
        "error_text"  => $locale['figurelib/admin/figurelib.php_031'],
        "inline"      => true,
        "width"       => "520px",
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
        "inline"      => true,
        "width"       => "520px",
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
        "inline"      => true,
        "width"       => "520px",
        "required"    => true,
        "no_root"     => 1,
        "query"       => (multilang_table("FI") ? "WHERE figure_measurements_language='".LANGUAGE."'" : ""),
        "maxselect"   => 1,
        "allowclear"  => true
    ], DB_FIGURE_MEASUREMENTS, "figure_measurements_inch", "figure_measurements_id", "figure_measurements_parent");

	// Formfield "Figure Material"
    echo form_select_tree("figure_material", $locale['figurelib/admin/figurelib.php_038'], $data['figure_material'], [
	    "placeholder" => $locale['figurelib/admin/figurelib.php_039'],
        "error_text"  => $locale['figurelib/admin/figurelib.php_040'],
        "inline"      => true,
        "width"       => "520px",
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
        "inline"      => true,
        "width"       => "520px",
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
        "inline"      => true,
        "width"       => "520px",
        "required"    => true,
        "no_root"     => 1,
        "query"       => (multilang_table("FI") ? "WHERE figure_packaging_language='".LANGUAGE."'" : ""),
        "maxselect"   => 1,
        "allowclear"  => true
    ], DB_FIGURE_PACKAGINGS, "figure_packaging_name", "figure_packaging_id", "figure_packaging_parent");

	// Dimsensions and More
    closeside();
	
/*#################################################################################################*/	
	
	// Prices / Limitations
    openside("<strong>PRICES & LIMITATIONS</strong>");
	
	// Formfield "Figure Pubdate"
	echo "<div id='figure_pubdate-field' class='form-group'>\n";
		echo "<label class='control-label col-xs-12 col-sm-3 col-md-3 col-lg-3 p-l-0' for='figure_pubdate'>". $locale['figurelib/admin/figurelib.php_047']."</label>\n";
		echo "<div class='col-xs-12 col-sm-9 col-md-9 col-lg-9'>\n";
			echo "<div class='input-group date' style='width: 520px;'>\n";
				echo "<input type='text' name='figure_pubdate' id='figure_pubdate' value='".$data['figure_pubdate']."' class='form-control textbox' placeholder='".$locale['figurelib/admin/figurelib.php_048']."' />\n";
				echo "<span class='input-group-addon'><i class='entypo calendar'></i></span>\n";
			echo "</div>\n";
		echo "</div>\n";
	echo "</div>\n";

	// Formfield "Figure Retailprice"
    echo form_text("figure_retailprice", $locale['figurelib/admin/figurelib.php_050'], $data['figure_retailprice'], [
		"placeholder"   => $locale['figurelib/admin/figurelib.php_051'],
        "inline"        => true,
        "width"         => "488px",
		"min"           => 0,
		"prepend_value" => "&#036;",
		"type"          => "number"
    ]);
	
	// Formfield "Figure Usedprice"
    echo form_text("figure_usedprice", $locale['figurelib/admin/figurelib.php_052'], $data['figure_usedprice'], [
		"placeholder"   => $locale['figurelib/admin/figurelib.php_053'],
        "inline"        => true,
        "width"         => "488px",
		"min"           => 0,
		"prepend_value" => "&#036;",
		"type"          => "number"
    ]);
	
	// Formfield "Figure Limited Edition"
    echo form_select_tree("figure_limitation", $locale['figurelib/admin/figurelib.php_054'], $data['figure_limitation'], [
	    "placeholder" => $locale['figurelib/admin/figurelib.php_055'],
        "error_text"  => $locale['figurelib/admin/figurelib.php_056'],
        "inline"      => true,
        "width"       => "520px",
        "required"    => true,
        "no_root"     => 1,
        "query"       => (multilang_table("FI") ? "WHERE figure_limitation_language='".LANGUAGE."'" : ""),
        "maxselect"   => 1,
        "allowclear"  => true
    ], DB_FIGURE_LIMITATIONS, "figure_limitation_name", "figure_limitation_id", "figure_limitation_parent");

	// Formfield "Figure Editions Size"
    echo form_text("figure_editionsize", $locale['figurelib/admin/figurelib.php_057'], $data['figure_editionsize'], [
		"placeholder"   => $locale['figurelib/admin/figurelib.php_058'],
        "inline"        => true,
        "width"         => "520px",
		"min"           => 0,
		"type"          => "number"
    ]);
	
	// Prices / Limitations
    closeside();
	
/*#################################################################################################*/	
	
	// VIDEO
    openside("<strong>VIDEO</strong>");
	
	// Formfield "Figure Video Url"
    echo form_text("figure_videourl", $locale['figure_461'], $data['figure_videourl'], [
		"placeholder"   => $locale['figure_1818'],
		"error_text"    => $locale['figurelib-error-113'],
        "inline"        => true,
        "width"         => "520px",
		"max_lenght"    => 40
    ]);

	// Diplay Video	
	echo "<hr />\n";
	echo "<div class='row'>\n";	
		echo "<div class='col-lg-3 col-md-3 col-sm-12 col-xs-12'>\n";
			echo "<strong>Video Preview</strong>\n";
		echo "</div>\n";									
		echo "<div class='col-lg-9 col-md-9 col-sm-12 col-xs-12'>\n";
			echo "<div id='figure_videopreview'></div>\n";
		echo "</div>\n";
	echo "</div>\n";					
				   
	// VIDEO
    closeside();

/*#################################################################################################*/	
	
	// Accessories and Discreptions
    openside("<strong>ACCESSORIES & DESCRIPTIONS</strong>");
	
	// Formfield "Accessories"
    echo form_textarea("figure_accessories", $locale['figurelib/admin/figurelib.php_060'], $data['figure_accessories'], [
		"type"      => fusion_get_settings("tinymce_enabled") ? "tinymce" : "html",
	    "tinymce"   => fusion_get_settings("tinymce_enabled") && iADMIN ? "advanced" : "simple",
	    "autosize"  => true,
	    "required"  => false,
	    "form_name" => "submit_form"
    ]);
	
	// Formfield "Description"
    echo form_textarea("figure_description", $locale['figurelib/admin/figurelib.php_061'], $data['figure_description'], [
		"type"      => fusion_get_settings("tinymce_enabled") ? "tinymce" : "html",
	    "tinymce"   => fusion_get_settings("tinymce_enabled") && iADMIN ? "advanced" : "simple",
	    "autosize"  => true,
	    "required"  => false,
	    "form_name" => "submit_form"
    ]);

	// Accessories and Discreptions
    closeside();
	
/*#################################################################################################*/	
	
	// Terms
    openside("");
	
	// Formfield "Terms"
	echo form_checkbox("figure_agb", $locale['figurelib/admin/figurelib.php_062'], $data['figure_agb'], ["reverse_label" => true, "required" => true, "error_text" => $locale['figurelib/admin/figurelib.php_063']]);

	// Terms
    closeside();

/*#################################################################################################*/	
	
	
	// Divider
    echo "<div class='btn-primary m-t-20'>\n";
    echo "<strong>EXTENDET ADMIN AREA</strong>";
    echo "</div>\n";
    echo "<br />\n";
	

	// Forum and E-Shop Url
    openside("<strong>FORUM & E-SHOP URL</strong>");
	
	// Formfield "Forum"
	echo form_text("figure_forum_url", $locale['figure_460'], $data['figure_forum_url'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "E-Shop Url"
	echo form_text("figure_eshop", $locale['figure_024'], $data['figure_eshop'], ["inline" => true, "width" => "520px"]);

	// Forum and E-Shop Url
    closeside();
	
/*#################################################################################################*/
	
	// Amazon
    openside("<strong>AMAZON LINKS</strong>");

	// Formfield "Amazon Germany"
	echo form_text("figure_amazon_de", "<img src='".INFUSIONS."figurelib/images/flags/flag_germany.png' alt='".$locale['figure_025']."' /> ".$locale['figure_025'], $data['figure_amazon_de'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Amazon United Kingdom"
	echo form_text("figure_amazon_uk", "<img src='".INFUSIONS."figurelib/images/flags/flag_great_britain.png' alt='".$locale['figure_026']."' /> ".$locale['figure_026'], $data['figure_amazon_uk'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Amazon France"
	echo form_text("figure_amazon_fr", "<img src='".INFUSIONS."figurelib/images/flags/flag_france.png' alt='".$locale['figure_027']."' /> ".$locale['figure_027'], $data['figure_amazon_fr'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Amazon Spain"
	echo form_text("figure_amazon_es", "<img src='".INFUSIONS."figurelib/images/flags/flag_spain.png' alt='".$locale['figure_028']."' /> ".$locale['figure_028'], $data['figure_amazon_es'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Amazon Italy"
	echo form_text("figure_amazon_it", "<img src='".INFUSIONS."figurelib/images/flags/flag_italy.png' alt='".$locale['figure_029']."' /> ".$locale['figure_029'], $data['figure_amazon_it'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Amazon Japan"
	echo form_text("figure_amazon_jp", "<img src='".INFUSIONS."figurelib/images/flags/flag_japan.png' alt='".$locale['figure_030']."' /> ".$locale['figure_030'], $data['figure_amazon_jp'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Amazon United States"
	echo form_text("figure_amazon_com", "<img src='".INFUSIONS."figurelib/images/flags/flag_usa.png' alt='".$locale['figure_031']."' /> ".$locale['figure_031'], $data['figure_amazon_com'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Amazon Canada"
	echo form_text("figure_amazon_ca", "<img src='".INFUSIONS."figurelib/images/flags/flag_canada.png' alt='".$locale['figure_032']."' /> ".$locale['figure_032'], $data['figure_amazon_ca'], ["inline" => true, "width" => "520px"]);
	
	// Amazon
    closeside();
	
/*#################################################################################################*/	
	
	// Affiliate Links
    openside("<strong>AFFILIATE LINKS</strong>");
	
	// Formfield "Affiliate Link #1"
	echo form_text("figure_affiliate_1", $locale['figure_023_1'], $data['figure_affiliate_1'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Affiliate Link #2"
	echo form_text("figure_affiliate_2", $locale['figure_023_2'], $data['figure_affiliate_2'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Affiliate Link #3"
	echo form_text("figure_affiliate_3", $locale['figure_023_3'], $data['figure_affiliate_3'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Affiliate Link #4"
	echo form_text("figure_affiliate_4", $locale['figure_023_4'], $data['figure_affiliate_4'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Affiliate Link #5"
	echo form_text("figure_affiliate_5", $locale['figure_023_5'], $data['figure_affiliate_5'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Affiliate Link #6"
	echo form_text("figure_affiliate_6", $locale['figure_023_6'], $data['figure_affiliate_6'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Affiliate Link #7"
	echo form_text("figure_affiliate_7", $locale['figure_023_7'], $data['figure_affiliate_7'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Affiliate Link #8"
	echo form_text("figure_affiliate_8", $locale['figure_023_8'], $data['figure_affiliate_8'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Affiliate Link #9"
	echo form_text("figure_affiliate_9", $locale['figure_023_9'], $data['figure_affiliate_9'], ["inline" => true, "width" => "520px"]);
	
	// Formfield "Affiliate Link #10"
	echo form_text("figure_affiliate_10", $locale['figure_023_10'], $data['figure_affiliate_10'], ["inline" => true, "width" => "520px"]);
	
	// Affiliate Links
    closeside();
	
/*#################################################################################################*/	
	
	// Buttons
    openside("");
	
	// Save Button
	echo form_button("save_figure", $locale['figurelib/admin/figurelib.php_064'], $locale['figurelib/admin/figurelib.php_064'], ["class" => "btn btn-default"]);

	// Delete Button
	if (isset($_GET['action']) && $_GET['action'] == "edit") {		
		echo "<a class='btn btn-default' href='".FUSION_SELF.$aidlink."&amp;section=figurelib_form&amp;action=delete&amp;figure_id=".$data['figure_id']."' onclick=\"return confirm('".$locale['film_0004']."');\"> ".$locale['cifg_0006']."</a>"; 
	}
	
	// Buttons
	closeside();
		
	// Form End
    echo closeform();

	
// Display Message, there are no Categories defined.
} else {
    echo "<div class='text-center'>\n";
		echo $locale['figurelib/admin/figurelib.php_065']."<br />\n";
		echo $locale['figurelib/admin/figurelib.php_066']."<br />\n<br />\n";
		echo "<a href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_categories'>".$locale['figurelib/admin/figurelib.php_067']."</a>\n";
		echo $locale['figurelib/admin/figurelib.php_068']."\n";
	echo "</div>\n";
}

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
		format: 'MMMM / YYYY',
});
</script>");
	

?>
