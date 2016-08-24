<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: figurelib_cats based on weblink_cats.php
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

// Delete a Category
if ((isset($_GET['action']) && $_GET['action'] == "delete") && (isset($_GET['cat_id']) && isnum($_GET['cat_id']))) {

	if (
		dbcount("(figure_cat)", DB_FIGURE_ITEMS, "figure_cat='".$_GET['cat_id']."'") ||
		dbcount("(figure_cat_id)", DB_FIGURE_CATS, "figure_cat_parent='".$_GET['cat_id']."'")
	) {
		addNotice("danger", $locale['figcm_0004'].$locale['figcm_0005']); 
	} else {
		figures_deleteImages("cats", $_GET['cat_id']);
		dbquery("DELETE FROM ".DB_FIGURE_CATS." WHERE figure_cat_id='".$_GET['cat_id']."'");
		addNotice("success", $locale['figcm_0003']);
	}
	redirect(clean_request("", ["section", "aid"], TRUE));

// Add / Edit a Category
} else {
	
	// Empty Arrays
	$data = [
		"figure_cat_id"          => 0,
		"figure_cat_name"        => "",
		"figure_cat_image"       => "",
		"figure_cat_thumb"       => "",
		"figure_cat_description" => "",
		"figure_cat_language"    => LANGUAGE,
		"figure_cat_parent"      => "",
		"figure_cat_sorting"     => "figure_title ASC"
	];
	
	// Edit a Category?
	if (
		(isset($_GET['action']) && $_GET['action'] == "edit") && 
		(isset($_GET['cat_id']) && isnum($_GET['cat_id'])) && 
		dbcount("(figure_cat_id)", DB_FIGURE_CATS, "figure_cat_id='".$_GET['cat_id']."'")
	) {
		$data = dbarray(dbquery("
			SELECT 
				figure_cat_id, figure_cat_name, figure_cat_image, figure_cat_thumb, figure_cat_description,
				figure_cat_language, figure_cat_parent, figure_cat_sorting
			FROM ".DB_FIGURE_CATS."
			WHERE figure_cat_id='".$_GET['cat_id']."'
			LIMIT 0,1
		"));
	}
	
	// Handle posted Informations
	if (isset($_POST['save_cat'])) {
		$data = [
			"figure_cat_id"          => form_sanitizer($_POST['figure_cat_id'],          0,        "figure_cat_id"),
			"figure_cat_name"        => form_sanitizer($_POST['figure_cat_name'],        "",       "figure_cat_name"),
			"figure_cat_description" => form_sanitizer($_POST['figure_cat_description'], "",       "figure_cat_description"),
			"figure_cat_language"    => form_sanitizer($_POST['figure_cat_language'],    LANGUAGE, "figure_cat_language"),
			"figure_cat_parent"      => form_sanitizer($_POST['figure_cat_parent'],      0,        "figure_cat_parent")
		];
		
		// Check Sorting Fields
		$catSortBy    = form_sanitizer($_POST['cat_sort_by'],    2,     "cat_sort_by");
		$catSortOrder = form_sanitizer($_POST['cat_sort_order'], "ASC", "cat_sort_order");
		
		// Handle Sorting Informations
		if (isnum($catSortBy) && $catSortBy == "1") {
			$data['figure_cat_sorting'] = "figure_id ".($catSortOrder == "ASC" ? "ASC" : "DESC");
		} elseif (isnum($catSortBy) && $catSortBy == "2") {
			$data['figure_cat_sorting'] = "figure_title ".($catSortOrder == "ASC" ? "ASC" : "DESC");
		} elseif (isnum($catSortBy) && $catSortBy == "3") {
			$data['figure_cat_sorting'] = "figure_datestamp ".($catSortOrder == "ASC" ? "ASC" : "DESC");
		} else {
			$data['figure_cat_sorting'] = "figure_title ASC";
		}
		
		// Handle Images
		if ($data['figure_cat_id'] && isset($_POST['delete_image'])) {
			figures_deleteImages("cats", $data['figure_cat_id']);
			$data['figure_cat_image'] = "";
			$data['figure_cat_thumb'] = "";
		}
		if (isset($_FILES['figure_cat_file']) && $_FILES['figure_cat_file']['name']) {
			if (!empty($_FILES['figure_cat_file']) && is_uploaded_file($_FILES['figure_cat_file']['tmp_name'])) {
				$upload = form_sanitizer($_FILES['figure_cat_file'], "", "figure_cat_file");
				if ($upload['error'] == 0) {
					figures_deleteImages("cats", $data['figure_cat_id']);
					$data['figure_cat_image'] = $upload['image_name'];
					$data['figure_cat_thumb'] = $upload['thumb1_name'];
				}
			}
		}
		if (!isset($data['figure_cat_image']) || !isset($data['figure_cat_thumb'])) {
			if ($data['figure_cat_id']) {
				$data['figure_cat_image'] = dbarraynum(dbquery("SELECT figure_cat_image FROM ".DB_FIGURE_CATS." WHERE figure_cat_id='".$data['figure_cat_id']."' LIMIT 0,1"));
				$data['figure_cat_thumb'] = dbarraynum(dbquery("SELECT figure_cat_thumb FROM ".DB_FIGURE_CATS." WHERE figure_cat_id='".$data['figure_cat_id']."' LIMIT 0,1"));
			} else {
				$data['figure_cat_image'] = "";
				$data['figure_cat_thumb'] = "";
			}
		}
		
		// Handle Category Check
		$catNameCheck = [
			"when_updating" => "figure_cat_name='".$data['figure_cat_name']."' AND figure_cat_id != '".$data['figure_cat_id']."'",
			"when_saving"   => "figure_cat_name='".$data['figure_cat_name']."'"
		];
		
		// Handle Action
		if (defender::safe()) {
			
			// Update a Figure
			if ($data['figure_cat_id']) {
				if (!dbcount("(figure_cat_id)", DB_FIGURE_CATS, $catNameCheck['when_updating'])) {
					dbquery_insert(DB_FIGURE_CATS, $data, "update");
					addNotice("success", $locale['figcm_0002']);
					redirect(clean_request("", ["section", "aid"], true));
				} else {
					$defender->stop();
					addNotice("danger", $locale['figcm_0006']);
				}
				
			// Save a Figure 
			} else {
				if (!dbcount("(figure_cat_id)", DB_FIGURE_CATS, $catNameCheck['when_saving'])) {
					dbquery_insert(DB_FIGURE_CATS, $data, "save");
					addNotice("success", $locale['figcm_0001']);
					redirect(clean_request("", ["section", "aid"], true));
				} else {
					$defender->stop();
					addNotice("danger", $locale['figcm_0006']);
				}
			}
		}
	}
	
	// Split Sorting Field
	$sorting = explode(" ", $data['figure_cat_sorting']);
		
	// Handle Sorting Informations
	if ($sorting[0] == "1") {
		$catSortBy = "figure_id";
	} elseif ($sorting[0] == "2") {
		$catSortBy = "figure_title";
	} elseif ($sorting[0] == "3") {
		$catSortBy = "figure_datestamp";
	} else {
		$catSortBy = "figure_title";
	}
	$catSortOrder = $sorting[1];

	// Handle Tab Informations
	$fiCategoryTab['title'] = [$locale['figc_0010'], $locale['filt_0004']]; 
	$fiCategoryTab['id']    = ["a", "b"];
	$tab_active             = tab_active($fiCategoryTab, isset($_GET['cat_view']) ? 1 : 0);
	
	// Display Tabs
	echo opentab($fiCategoryTab, $tab_active, "fiCategory_Tab", false, "m-t-20");
	echo opentabbody($fiCategoryTab['title'][0], $fiCategoryTab['id'][0], $tab_active);
	
	// Open Form
	echo openform("addcat", "post", FUSION_REQUEST, ["class" => "m-t-20", "enctype" => true]);
	echo form_hidden("figure_cat_id", "", $data['figure_cat_id']);

	// Category Name
	echo form_text("figure_cat_name", $locale['figc_0000'], $data['figure_cat_name'], [
		"inline"     => true,
		"required"   => true,
		"error_text" => $locale['figc_0001']
	]);
	
	// Category Image
	echo form_fileinput("figure_cat_file", "Category Image", "", [
		"upload_path"      => CATEGORIES,
		"type"             => "image",
		"thumbnail"        => true,
		"thumbnail_w"      => $figurelibSettings['figure_thumb_cat_w'],
		"thumbnail_h"      => $figurelibSettings['figure_thumb_cat_h'],
		"thumbnail_folder" => "thumbs/",
		"thumbnail_suffix" => "_t1",
		"delete_original"  => false,
		"max_width"        => $figurelibSettings['figure_photo_cat_max_w'],
		"max_height"       => $figurelibSettings['figure_photo_cat_max_h'],
		"max_byte"         => $figurelibSettings['figure_photo_cat_max_b'],
		"max_count"        => 1
	]);
	
	// Display Category Image
	if ($data['figure_cat_image'] || $data['figure_cat_thumb']) {
		echo "<div class='row'>\n";
			echo "<div class='col-sm-3 col-xs-12'></div>\n";
			echo "<div class='col-sm-9 col-xs-12'>\n";
				echo form_checkbox("delete_image", "Delete this Image <br /> <img src='".figures_getImagePath("cats", "thumb", $data['figure_cat_id'])."' style='max-width: 200px;' />", "0", [
					"reverse_label" => true
				]);
			echo "</div>\n";
		echo "</div>\n";
	}
	
	// Category Description							 
	echo form_textarea("figure_cat_description", $locale['figc_0002'], $data['figure_cat_description'], [
		"inline"    => true,
		"html"      => true,
		"preview"   => false,
		"autosize"  => true,
		"form_name" => "addcat"
	]);
	
	// Category Parent
	echo form_select_tree("figure_cat_parent", $locale['figc_0003'], $data['figure_cat_parent'], [
		"disable_opts"  => [$data['figure_cat_id']],
		"hide_disabled" => true,
		"inline"        => true,
	], DB_FIGURE_CATS, "figure_cat_name", "figure_cat_id", "figure_cat_parent");
	
	// Category Language
	if (multilang_table("FI")) {
		echo form_select("figure_cat_language", $locale['global_ML100'], $data['figure_cat_language'], [
			"options" => fusion_get_enabled_languages(),
			"inline" => true,
		]);
	} else {
		echo form_hidden("figure_cat_language", "", $data['figure_cat_language']);
	}
	
	// Category Sorting
	echo "<div class='row m-0'>\n";	
	echo "<label class='label-control col-xs-12 col-sm-3 p-l-0'>".$locale['figc_0004']."</label>\n";
	echo "<div class='col-xs-12 col-sm-3 p-l-0'>\n";

		// Category Sorting By
		echo form_select("cat_sort_by", "", $catSortBy, [
			"inline"  => true,
			"width"   => "100%",
			"options" => ["1" => $locale['figc_0005'], "2" => $locale['figc_0006'], "3" => $locale['figc_0007']],
			"class"   => "pull-left m-r-10"
		]);
		
	echo "</div>\n";
	echo "<div class='col-xs-12 col-sm-3'>\n";
	
		// Category Sorting Direction
		echo form_select("cat_sort_order", "", $catSortOrder, [
			"inline"  => true,
			"width"   => "100%",
			"options" => ["ASC" => $locale['figc_0008'], "DESC" => $locale['figc_0009']]
		]);
		
	echo "</div>\n";
	echo "</div>\n";
		
	// Button
	echo form_button("save_cat", $locale['figc_0011'], $locale['figc_0011'], ["class" => "btn-primary m-t-10"]);
	
	// Close Form
	echo closeform();
	
	// Display Tabs
	echo closetabbody();
	
	// Display Tabs
	echo opentabbody($fiCategoryTab['title'][1], $fiCategoryTab['id'][1], $tab_active);
	figures_showcatlist();
	echo closetabbody();
	echo closetab();

}

/////////////////////////////////////////////////////////
// FUNCTIONS ////////////////////////////////////////////
/////////////////////////////////////////////////////////
function figures_showcatlist($parent = 0, $level = 0) {
	global $locale, $aidlink;
	
	// Check Page (only on Level 0)
	if ($parent == "0" && $level == "0") {
		$allRows = dbcount("(figure_cat_id)", DB_FIGURE_CATS, "figure_cat_parent='0'".(multilang_table("FI") ? " AND figure_cat_language='".LANGUAGE."'" : "")."");
		if (isset($_GET['rowstart']) && isNum($_GET['rowstart']) && $_GET['rowstart'] <= $allRows) {
			$rowstart = $_GET['rowstart'];
		} else {
			$rowstart = 0;
		}
	}
	
	// Get all Category
	$result = dbquery("
		SELECT 
			figure_cat_id, 
			figure_cat_name, 
			figure_cat_description,
			figure_cat_image,
			figure_cat_thumb
		FROM ".DB_FIGURE_CATS." 
		WHERE figure_cat_parent='".$parent."'".(multilang_table("FI") ? " AND figure_cat_language='".LANGUAGE."'" : "")." 
		ORDER BY figure_cat_name ASC
		".($parent == "0" && $level == "0" ? "LIMIT ".$rowstart.",10" : "")."
	");
	
	// If there are Results, display it.
	if (dbrows($result) != 0) {
		if ($parent == "0" && $level == "0") { echo "<table class='table table-responsive table-hover table-striped'>\n"; }
		
		// Display Items
		while ($data = dbarray($result)) {
			$description = strip_tags(parse_textarea($data['figure_cat_description']));
			
			// Display each Item
			echo "<tr>\n";
				if ($data['figure_cat_thumb'] || $data['figure_cat_image']) {
					echo "<td class='figurelib-inforow-height' style='width: 1%;'><img src='".figures_getImagePath("cats", "thumb", $data['figure_cat_id'])."' alt='Category Image' /></td>\n";
				} else {
					echo "<td style='width: 1%;'></td>\n";
				}
				echo "<td><strong>".str_repeat("&mdash;", $level)." ".$data['figure_cat_name']."</strong>\n";
				if ($data['figure_cat_description']) {
					echo "<br />".str_repeat("&mdash;", $level)." <span class='small'>".$description."</span>\n";
				}
				echo "</td>\n";
				echo "<td align='center' width='1%' style='white-space:nowrap'>\n";
					echo "<a href='".FUSION_SELF.$aidlink."&amp;section=figurelib_categories&amp;action=edit&amp;cat_id=".$data['figure_cat_id']."' title='".$locale['cifg_0005']."' class='btn btn-warning btn-xs'><i class='fa fa-fw fa-cogs'></i> ".$locale['cifg_0005']."</a>\n"; 
					echo "<a href='".FUSION_SELF.$aidlink."&amp;section=figurelib_categories&amp;action=delete&amp;cat_id=".$data['figure_cat_id']."' title='".$locale['cifg_0006']."' onclick=\"return confirm('".$locale['figcm_0007']."');\" class='btn btn-danger btn-xs'><i class='fa fa-fw fa-trash'></i> ".$locale['cifg_0006']."</a>\n";
				echo "</td>\n";
			echo "</tr>\n";
			
			// Display Sublevel
			figures_showcatlist($data['figure_cat_id'], $level+1);
		}
		if ($parent == "0" && $level == "0") {  echo "</table>\n"; }
		
		// Display Navigation (only on Level 0)
		if ($parent == "0" && $level == "0") {
			if ($allRows > 10) {
				echo "<hr />\n";
				echo "<div class='text-center'>\n";
					echo makepagenav($rowstart, 10, $allRows, 6, FUSION_SELF.$aidlink."&amp;section=figurelib_categories&amp;cat_view&amp;");
				echo "\n</div>\n";
			}
		}
		
	// Otherwise display Message
	} else {
		if ($parent == "0" && $level == "0") {
			echo "<div class='text-center tbl1'>".$locale['figc_0012']."</div>\n";
		}
	}
}

?>