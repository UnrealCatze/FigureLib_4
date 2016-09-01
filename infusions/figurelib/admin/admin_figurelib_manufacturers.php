<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: figurelib_manufacturers based on weblink_cats.php
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

if (!defined("IN_FUSION")) { die("Access Denied"); }
$locale = fusion_get_locale();
global $figurelibSettings;
pageAccess("FI");

// Delete a Manufacturer
if ((isset($_GET['action']) && $_GET['action'] == "delete") && (isset($_GET['manufacturer_id']) && isnum($_GET['manufacturer_id']))) {

	if (
		dbcount("(figure_manufacturer)", DB_FIGURE_ITEMS, "figure_manufacturer='".$_GET['manufacturer_id']."'") ||
		dbcount("(figure_manufacturer_id)", DB_FIGURE_MANUFACTURERS, "figure_manufacturer_parent='".$_GET['manufacturer_id']."'")
	) {
		addNotice("danger", $locale['figmm_0004'].$locale['figmm_0005']); 
	} else {
		figures_deleteImages("manufacturers", $_GET['manufacturer_id']);
		dbquery("DELETE FROM ".DB_FIGURE_MANUFACTURERS." WHERE figure_manufacturer_id='".$_GET['manufacturer_id']."'");
		addNotice("success", $locale['figmm_0003']);
	}
	redirect(clean_request("", ["section", "aid"], TRUE));

// Add / Edit a Manufacturer
} else {
	
	// Empty Arrays
	$data = [
		"figure_manufacturer_id"          		=> 0,
		"figure_manufacturer_name"        		=> "",
		"figure_manufacturer_image"      		=> "",
		"figure_manufacturer_thumb"      		=> "",
		"figure_manufacturer_url"      	  		=> "",
		"figure_manufacturer_email"      		=> "",
		"figure_manufacturer_facebook"    		=> "",
		"figure_manufacturer_twitter"     		=> "",
		"figure_manufacturer_youtube"     		=> "",
		"figure_manufacturer_pinterest"     	=> "",
		"figure_manufacturer_instagram"     	=> "",
		"figure_manufacturer_googleplus"     	=> "",
		"figure_manufacturer_affiliate_program" => "",
		"figure_manufacturer_affiliate_url"    	=> "",
		"figure_manufacturer_affiliate_code"    => "",
		"figure_manufacturer_info_admin"     	=> "",
		"figure_manufacturer_address"        	=> "",
		"figure_manufacturer_description" 		=> "",
		"figure_manufacturer_language"    		=> LANGUAGE,
		"figure_manufacturer_parent"      		=> "",
		"figure_manufacturer_sorting"     		=> "figure_title ASC"
	];
	
	// Edit a Manufacturer?
	if (
		(isset($_GET['action']) && $_GET['action'] == "edit") && 
		(isset($_GET['manufacturer_id']) && isnum($_GET['manufacturer_id'])) && 
		dbcount("(figure_manufacturer_id)", DB_FIGURE_MANUFACTURERS, "figure_manufacturer_id='".$_GET['manufacturer_id']."'")
	) {
		$data = dbarray(dbquery("
			SELECT *
			FROM ".DB_FIGURE_MANUFACTURERS."
			WHERE figure_manufacturer_id='".$_GET['manufacturer_id']."'
			LIMIT 0,1
		"));
	}
	
	// Handle posted Informations
	if (isset($_POST['save_manufacturer'])) {
		$data = [
			"figure_manufacturer_id"          		=> form_sanitizer($_POST['figure_manufacturer_id'],          		0,        "figure_manufacturer_id"),
			"figure_manufacturer_name"        		=> form_sanitizer($_POST['figure_manufacturer_name'],        		"",       "figure_manufacturer_name"),		
			"figure_manufacturer_url"      	  		=> form_sanitizer($_POST['figure_manufacturer_url'],         		"",       "figure_manufacturer_url"),
			"figure_manufacturer_email"       		=> form_sanitizer($_POST['figure_manufacturer_email'],       		"",       "figure_manufacturer_email"),
			"figure_manufacturer_facebook"   	 	=> form_sanitizer($_POST['figure_manufacturer_facebook'],    		"",       "figure_manufacturer_facebook"),
			"figure_manufacturer_twitter"    	 	=> form_sanitizer($_POST['figure_manufacturer_twitter'],     		"",       "figure_manufacturer_twitter"),
			"figure_manufacturer_youtube"     		=> form_sanitizer($_POST['figure_manufacturer_youtube'],     		"",       "figure_manufacturer_youtube"),
			"figure_manufacturer_pinterest"     	=> form_sanitizer($_POST['figure_manufacturer_pinterest'],     		"",       "figure_manufacturer_pinterest"),
			"figure_manufacturer_instagram"     	=> form_sanitizer($_POST['figure_manufacturer_instagram'],     		"",       "figure_manufacturer_instagram"),
			"figure_manufacturer_googleplus"     	=> form_sanitizer($_POST['figure_manufacturer_googleplus'],     	"",       "figure_manufacturer_googleplus"),
			"figure_manufacturer_affiliate_program" => form_sanitizer($_POST['figure_manufacturer_affiliate_program'],  "",       "figure_manufacturer_affiliate_program"),
			"figure_manufacturer_affiliate_url"     => form_sanitizer($_POST['figure_manufacturer_affiliate_url'],     	"",       "figure_manufacturer_affiliate_url"),
			"figure_manufacturer_affiliate_code"    => form_sanitizer($_POST['figure_manufacturer_affiliate_code'],     "",       "figure_manufacturer_affiliate_code"),
			"figure_manufacturer_info_admin"     	=> form_sanitizer($_POST['figure_manufacturer_info_admin'],     	"",       "figure_manufacturer_info_admin"),		
			"figure_manufacturer_address"        		=> form_sanitizer($_POST['figure_manufacturer_address'],        		"",       "figure_manufacturer_address"),			
			"figure_manufacturer_description" 		=> form_sanitizer($_POST['figure_manufacturer_description'], 		"",       "figure_manufacturer_description"),
			"figure_manufacturer_language"    		=> form_sanitizer($_POST['figure_manufacturer_language'],    		LANGUAGE, "figure_manufacturer_language"),
			"figure_manufacturer_parent"      		=> form_sanitizer($_POST['figure_manufacturer_parent'],      		0,        "figure_manufacturer_parent")
		];
		
		// Check Sorting Fields
		$manufacturerSortBy    = form_sanitizer($_POST['manufacturer_sort_by'],    2,     "manufacturer_sort_by");
		$manufacturerSortOrder = form_sanitizer($_POST['manufacturer_sort_order'], "ASC", "manufacturer_sort_order");
		
		// Handle Sorting Informations
		if (isnum($manufacturerSortBy) && $manufacturerSortBy == "1") {
			$data['figure_manufacturer_sorting'] = "figure_id ".($manufacturerSortOrder == "ASC" ? "ASC" : "DESC");
		} elseif (isnum($manufacturerSortBy) && $manufacturerSortBy == "2") {
			$data['figure_manufacturer_sorting'] = "figure_title ".($manufacturerSortOrder == "ASC" ? "ASC" : "DESC");
		} elseif (isnum($manufacturerSortBy) && $manufacturerSortBy == "3") {
			$data['figure_manufacturer_sorting'] = "figure_datestamp ".($manufacturerSortOrder == "ASC" ? "ASC" : "DESC");
		} else {
			$data['figure_manufacturer_sorting'] = "figure_title ASC";
		}
		
		// Handle Images
		if ($data['figure_manufacturer_id'] && isset($_POST['delete_image'])) {
			figures_deleteImages("manufacturers", $data['figure_manufacturer_id']);
			$data['figure_manufacturer_image'] = "";
			$data['figure_manufacturer_thumb'] = "";
		}
		if (isset($_FILES['figure_manufacturer_file']) && $_FILES['figure_manufacturer_file']['name']) {
			if (!empty($_FILES['figure_manufacturer_file']) && is_uploaded_file($_FILES['figure_manufacturer_file']['tmp_name'])) {
				$upload = form_sanitizer($_FILES['figure_manufacturer_file'], "", "figure_manufacturer_file");
				if ($upload['error'] == 0) {
					figures_deleteImages("manufacturers", $data['figure_manufacturer_id']);
					$data['figure_manufacturer_image'] = $upload['image_name'];
					$data['figure_manufacturer_thumb'] = $upload['thumb1_name'];
				}
			}
		}
		if (!isset($data['figure_manufacturer_image']) || !isset($data['figure_manufacturer_thumb'])) {
			if ($data['figure_manufacturer_id']) {
				@list($data['figure_manufacturer_image']) = dbarraynum(dbquery("SELECT figure_manufacturer_image FROM ".DB_FIGURE_MANUFACTURERS." WHERE figure_manufacturer_id='".$data['figure_manufacturer_id']."' LIMIT 0,1"));
				@list($data['figure_manufacturer_thumb']) = dbarraynum(dbquery("SELECT figure_manufacturer_thumb FROM ".DB_FIGURE_MANUFACTURERS." WHERE figure_manufacturer_id='".$data['figure_manufacturer_id']."' LIMIT 0,1"));
			} else {
				$data['figure_manufacturer_image'] = "";
				$data['figure_manufacturer_thumb'] = "";
			}
		}
		
		// Handle Manufacturer Check
		$manufacturerNameCheck = [
			"when_updating" => "figure_manufacturer_name='".$data['figure_manufacturer_name']."' AND figure_manufacturer_id != '".$data['figure_manufacturer_id']."'",
			"when_saving"   => "figure_manufacturer_name='".$data['figure_manufacturer_name']."'"
		];
		
		// Handle Action
		if (defender::safe()) {
			
			// Update a Figure
			if ($data['figure_manufacturer_id']) {
				if (!dbcount("(figure_manufacturer_id)", DB_FIGURE_MANUFACTURERS, $manufacturerNameCheck['when_updating'])) {
					dbquery_insert(DB_FIGURE_MANUFACTURERS, $data, "update");
					addNotice("success", $locale['figmm_0002']);
					redirect(clean_request("", ["section", "aid"], true));
				} else {
					$defender->stop();
					addNotice("danger", $locale['figmm_0006']);
				}
				
			// Save a Figure 
			} else {
				if (!dbcount("(figure_manufacturer_id)", DB_FIGURE_MANUFACTURERS, $manufacturerNameCheck['when_saving'])) {
					dbquery_insert(DB_FIGURE_MANUFACTURERS, $data, "save");
					addNotice("success", $locale['figmm_0001']);
					redirect(clean_request("", ["section", "aid"], true));
				} else {
					$defender->stop();
					addNotice("danger", $locale['figmm_0006']);
				}
			}
		}
	}
	
	// Split Sorting Field
	$sorting = explode(" ", $data['figure_manufacturer_sorting']);
		
	// Handle Sorting Informations
	if ($sorting[0] == "1") {
		$manufacturerSortBy = "figure_id";
	} elseif ($sorting[0] == "2") {
		$manufacturerSortBy = "figure_title";
	} elseif ($sorting[0] == "3") {
		$manufacturerSortBy = "figure_datestamp";
	} else {
		$manufacturerSortBy = "figure_title";
	}
	$manufacturerSortOrder = $sorting[1];

	// Handle Tab Informations
	$fiManufacturerTab['title'] = [$locale['figm_0010'], $locale['filt_0014']]; 
	$fiManufacturerTab['id']    = ["a", "b"];
	$tab_active                 = tab_active($fiManufacturerTab, isset($_GET['manufacturer_view']) ? 1 : 0);
	
	// Display Tabs
	echo opentab($fiManufacturerTab, $tab_active, "fiManufacturer_Tab", false, "m-t-20");
	echo opentabbody($fiManufacturerTab['title'][0], $fiManufacturerTab['id'][0], $tab_active);
	
	// Open Form
	echo openform("addmanufacturer", "post", FUSION_REQUEST, ["class" => "m-t-20", "enctype" => true]);
	echo form_hidden("figure_manufacturer_id", "", $data['figure_manufacturer_id']);

	// Manufacturer Name
	echo form_text("figure_manufacturer_name", $locale['figm_0000'], $data['figure_manufacturer_name'], [
		"inline"     => true,
		"required"   => true,
		"error_text" => $locale['figm_0001']
	]);
	
	// Manufacturer Image
	echo form_fileinput("figure_manufacturer_file", "Manufacturer Image", "", [
		"upload_path"       => MANUFACTURERS,
		"type"              => "image",
		"thumbnail"         => true,
		"thumbnail_w"       => $figurelibSettings['figure_thumb_man_w'],
		"thumbnail_h"       => $figurelibSettings['figure_thumb_man_h'],
		"thumbnail_folder"  => "thumbs/",
		"thumbnail_suffix"  => "_t1",
		"delete_original"   => false,
		"max_width"         => $figurelibSettings['figure_photo_man_max_w'],
		"max_height"        => $figurelibSettings['figure_photo_man_max_h'],
		"max_byte"          => $figurelibSettings['figure_photo_man_max_b'],
		"max_count"         => 1
	]);
	
	// Display Manufacturer Image
	if ($data['figure_manufacturer_image'] || $data['figure_manufacturer_thumb']) {
		echo "<div class='row'>\n";
			echo "<div class='col-sm-3 col-xs-12'></div>\n";
			echo "<div class='col-sm-9 col-xs-12'>\n";
				echo form_checkbox("delete_image", "Delete this Image <br /> <img src='".figures_getImagePath("manufacturers", "thumb", $data['figure_manufacturer_id'])."' style='max-width: 200px;' />", "0", [
					"reverse_label" => true
				]);
			echo "</div>\n";
		echo "</div>\n";
	}
		
	// Manufacturer URL
	echo form_text("figure_manufacturer_url", $locale['figm_0017'], $data['figure_manufacturer_url'], [
		"inline"     => true,
		"type"		 => "url",
		"required"   => false,
		"error_text" => $locale['figm_0001']
	]);
	
	// Manufacturer E-MAIL
	echo form_text("figure_manufacturer_email", $locale['figm_0018'], $data['figure_manufacturer_email'], [
		"inline"     => true,
		"required"   => false,
		"type"		 => "email",	
		"error_text" => $locale['figm_0001']
	]);
	
	// Manufacturer FACEBOOK
	echo form_text("figure_manufacturer_facebook", $locale['figm_0019'], $data['figure_manufacturer_facebook'], [
		"inline"     => true,
		"type"		 => "url",
		"required"   => false,
		"error_text" => $locale['figm_0001']
	]);
	
	// Manufacturer TWITTER
	echo form_text("figure_manufacturer_twitter", $locale['figm_0020'], $data['figure_manufacturer_twitter'], [
		"inline"     => true,
		"type"		 => "url",
		"required"   => false,
		"error_text" => $locale['figm_0001']
	]);
	
	// Manufacturer YOUTUBE CHANNEL
	echo form_text("figure_manufacturer_youtube", $locale['figm_0021'], $data['figure_manufacturer_youtube'], [
		"inline"     => true,
		"type"		 => "url",
		"required"   => false,
		"error_text" => $locale['figm_0001']
	]);
		
	// ['figm_0023'] = "Manufacturer Pinterest";
	echo form_text("figure_manufacturer_pinterest", $locale['figm_0023'], $data['figure_manufacturer_pinterest'], [
		"inline"     => true,
		"type"		 => "url",
		"required"   => false,
		"error_text" => $locale['figm_0001']
	]);
	
	// ['figm_0024'] = "Manufacturer Instagram";
	echo form_text("figure_manufacturer_instagram", $locale['figm_0024'], $data['figure_manufacturer_instagram'], [
		"inline"     => true,
		"type"		 => "url",
		"required"   => false,
		"error_text" => $locale['figm_0001']
	]);
	
	// ['figm_0025'] = "Manufacturer Google+";
	echo form_text("figure_manufacturer_googleplus", $locale['figm_0025'], $data['figure_manufacturer_googleplus'], [
		"inline"     => true,
		"type"		 => "url",
		"required"   => false,
		"error_text" => $locale['figm_0001']
	]);	


	// Set Options for Selectlists
		$affiliate_opts = [
			'0' => $locale['no'], 
			'1' => $locale['yes']
		];	
	
	// ['figm_0026'] = "Manufacturer Affiliate Program";
	echo form_select("figure_manufacturer_affiliate_program", $locale['figm_0026'], $data['figure_manufacturer_affiliate_program'], 
		array(
		"inline" => TRUE, 
		"options" => $affiliate_opts
		));	
			
	// ['figm_0027'] = "Manufacturer Affiliate URL";
	echo form_text("figure_manufacturer_affiliate_url", $locale['figm_0027'], $data['figure_manufacturer_affiliate_url'], [
		"inline"     => true,
		"type"		 => "url",
		"required"   => false,
		"error_text" => $locale['figm_0001']
	]);	

	// ['figm_0028'] = "Manufacturer Affiliate Code";
	echo form_text("figure_manufacturer_affiliate_code", $locale['figm_0028'], $data['figure_manufacturer_affiliate_code'], [
		"inline"     => true,
		"required"   => false,
		"error_text" => $locale['figm_0001']
	]);	
	
	
	// ['figm_0029'] = "Manufacturer Info Admin";
	echo form_textarea("figure_manufacturer_info_admin", $locale['figm_0029'], $data['figure_manufacturer_info_admin'], [
		"type"      => fusion_get_settings("tinymce_enabled") ? "tinymce" : "html",
		"tinymce"   => fusion_get_settings("tinymce_enabled") && iADMIN ? "advanced" : "simple",
		#"autosize"  => true,
		"required"  => false,
		"form_name" => "addmanufacturer"
		]);
				
	// Manufacturer Address						 
	echo form_textarea("figure_manufacturer_address", $locale['figm_0022'], $data['figure_manufacturer_address'], [
		"type"      => fusion_get_settings("tinymce_enabled") ? "tinymce" : "html",
		"tinymce"   => fusion_get_settings("tinymce_enabled") && iADMIN ? "advanced" : "simple",
		#"autosize"  => true,
		"required"  => false,
		"form_name" => "addmanufacturer"
	]);
		
	// Manufacturer Description							 
	echo form_textarea("figure_manufacturer_description", $locale['figm_0002'], $data['figure_manufacturer_description'], [
		"type"      => fusion_get_settings("tinymce_enabled") ? "tinymce" : "html",
		"tinymce"   => fusion_get_settings("tinymce_enabled") && iADMIN ? "advanced" : "simple",
		#"autosize"  => true,
		"required"  => false,
		"form_name" => "addmanufacturer"
	]);
	
	// Manufacturer Parent
	echo form_select_tree("figure_manufacturer_parent", $locale['figm_0003'], $data['figure_manufacturer_parent'], [
		"disable_opts"  => [$data['figure_manufacturer_id']],
		"hide_disabled" => true,
		"inline"        => true,
	], DB_FIGURE_MANUFACTURERS, "figure_manufacturer_name", "figure_manufacturer_id", "figure_manufacturer_parent");
	
	// Manufacturer Language
	if (multilang_table("FI")) {
		echo form_select("figure_manufacturer_language", $locale['global_ML100'], $data['figure_manufacturer_language'], [
			"options" => fusion_get_enabled_languages(),
			"inline" => true,
		]);
	} else {
		echo form_hidden("figure_manufacturer_language", "", $data['figure_manufacturer_language']);
	}
	
	// Manufacturer Sorting
	echo "<div class='row m-0'>\n";	
	echo "<label class='label-control col-xs-12 col-sm-3 p-l-0'>".$locale['figm_0004']."</label>\n";
	echo "<div class='col-xs-12 col-sm-3 p-l-0'>\n";

		// Manufacturer Sorting By
		echo form_select("manufacturer_sort_by", "", $manufacturerSortBy, [
			"inline"  => true,
			"width"   => "100%",
			"options" => ["1" => $locale['figm_0005'], "2" => $locale['figm_0006'], "3" => $locale['figm_0007']],
			"class"   => "pull-left m-r-10"
		]);
		
	echo "</div>\n";
	echo "<div class='col-xs-12 col-sm-3'>\n";
	
		// Manufacturer Sorting Direction
		echo form_select("manufacturer_sort_order", "", $manufacturerSortOrder, [
			"inline"  => true,
			"width"   => "100%",
			"options" => ["ASC" => $locale['figm_0008'], "DESC" => $locale['figm_0009']]
		]);
		
	echo "</div>\n";
	echo "</div>\n";
		
	// Button
	echo form_button("save_manufacturer", $locale['figm_0011'], $locale['figm_0011'], ["class" => "btn-primary m-t-10"]);
	
	// Close Form
	echo closeform();
	
	// Display Tabs
	echo closetabbody();
	
	// Display Tabs
	echo opentabbody($fiManufacturerTab['title'][1], $fiManufacturerTab['id'][1], $tab_active);
	figures_showmanufacturerlist();
	echo closetabbody();
	echo closetab();

}

/////////////////////////////////////////////////////////
// FUNCTIONS ////////////////////////////////////////////
/////////////////////////////////////////////////////////
	function figures_showmanufacturerlist($parent = 0, $level = 0) {
		global $locale, $aidlink;
		
		// Check Page (only on Level 0)
		if ($parent == "0" && $level == "0") {
			$allRows = dbcount("(figure_manufacturer_id)", DB_FIGURE_MANUFACTURERS, "figure_manufacturer_parent='0'".(multilang_table("FI") ? " AND figure_manufacturer_language='".LANGUAGE."'" : "")."");
			if (isset($_GET['rowstart']) && isNum($_GET['rowstart']) && $_GET['rowstart'] <= $allRows) {
				$rowstart = $_GET['rowstart'];
			} else {
				$rowstart = 0;
			}
		}
		
		// Get all Manufacturer
		/*
		$result = dbquery("
			SELECT 
				figure_manufacturer_id, 
				figure_manufacturer_name, 
				figure_manufacturer_description,
				figure_manufacturer_image,
				figure_manufacturer_thumb
			FROM ".DB_FIGURE_MANUFACTURERS." 
			WHERE figure_manufacturer_parent='".$parent."'".(multilang_table("FI") ? " AND figure_manufacturer_language='".LANGUAGE."'" : "")." 
			ORDER BY figure_manufacturer_name ASC
			".($parent == "0" && $level == "0" ? "LIMIT ".$rowstart.",10" : "")."
		");
		*/
		
		// figure_manufacturer_description auskommentiert damit die description nicht in der liste angezeigt wird für den hersteller. 
		$result = dbquery("
			SELECT 
				figure_manufacturer_id, 
				figure_manufacturer_name, 
				figure_manufacturer_image,
				figure_manufacturer_thumb
			FROM ".DB_FIGURE_MANUFACTURERS." 
			WHERE figure_manufacturer_parent='".$parent."'".(multilang_table("FI") ? " AND figure_manufacturer_language='".LANGUAGE."'" : "")." 
			ORDER BY figure_manufacturer_name ASC
			".($parent == "0" && $level == "0" ? "LIMIT ".$rowstart.",10" : "")."
		");
		
		// If there are Results, display it.
		if (dbrows($result) != 0) {
			if ($parent == "0" && $level == "0") {  echo "<table class='table table-responsive table-hover table-striped'>\n"; }			
			// Display Items
			while ($data = dbarray($result)) {
				//auskommentiert damit die description nicht in der liste angezeigt wird für den hersteller. 
				//$description = strip_tags(parse_textarea($data['figure_manufacturer_description']));
				
				// Display each Item
				echo "<tr>\n";
					if ($data['figure_manufacturer_thumb'] || $data['figure_manufacturer_image']) {
						echo "<td class='figurelib-inforow-height' style='width: 1%;'><img src='".figures_getImagePath("manufacturers", "thumb", $data['figure_manufacturer_id'])."' alt='Manufacturer Image' /></td>\n";
					} else {
						echo "<td style='width: 1%;'></td>\n";
					}
					echo "<td><strong>".str_repeat("&mdash;", $level).$data['figure_manufacturer_name']."</strong>\n";
					//auskommentiert damit die description nicht in der liste angezeigt wird für den hersteller. 
					//if ($data['figure_manufacturer_description']) {
						//echo "<br />".str_repeat("&mdash;", $level)."<span class='small'>".$description."</span>\n";
					//}
					echo "</td>\n";
					echo "<td align='center' width='1%' style='white-space:nowrap'>\n";
						echo "<a href='".FUSION_SELF.$aidlink."&amp;section=figurelib_manufacturers&amp;action=edit&amp;manufacturer_id=".$data['figure_manufacturer_id']."' title='".$locale['cifg_0005']."' class='btn btn-warning btn-xs'><i class='fa fa-fw fa-cogs'></i> ".$locale['cifg_0005']."</a>\n"; 
						echo "<a href='".FUSION_SELF.$aidlink."&amp;section=figurelib_manufacturers&amp;action=delete&amp;manufacturer_id=".$data['figure_manufacturer_id']."' onclick=\"return confirm('".$locale['figmm_0007']."');\" class='btn btn-danger btn-xs'><i class='fa fa-fw fa-trash'></i> ".$locale['cifg_0006']."</a>\n";
					echo "</td>\n";
				echo "</tr>\n";
				
				// Display Sublevel
				figures_showmanufacturerlist($data['figure_manufacturer_id'], $level+1);
			}
			if ($parent == "0" && $level == "0") { echo "</table>\n"; }
			
			// Display Navigation (only on Level 0)
			if ($parent == "0" && $level == "0") {
				if ($allRows > 10) {
					echo "<hr />\n";
					echo "<div class='text-center'>\n";
						echo makepagenav($rowstart, 10, $allRows, 6, FUSION_SELF.$aidlink."&amp;section=figurelib_manufacturers&amp;manufacturer_view&amp;");
					echo "\n</div>\n";
				}
			}
			
		// Otherwise display Message
		} else {
			if ($parent == "0" && $level == "0") {
				echo "<div class='text-center tbl1'>".$locale['figm_0012']."</div>\n";
			}
		}
	}

?>