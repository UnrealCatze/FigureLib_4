<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: figurelib_brands based on weblink_cats.php
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
pageAccess("FI");

// Delete a Brand
if ((isset($_GET['action']) && $_GET['action'] == "delete") && (isset($_GET['brand_id']) && isnum($_GET['brand_id']))) {

	if (
		dbcount("(figure_brand)", DB_FIGURE_ITEMS, "figure_brand='".$_GET['brand_id']."'") ||
		dbcount("(figure_brand_id)", DB_FIGURE_BRANDS, "figure_brand_parent='".$_GET['brand_id']."'")
	) {
		addNotice("danger", $locale['figbm_0004'].$locale['figbm_0005']); 
	} else {
		dbquery("DELETE FROM ".DB_FIGURE_BRANDS." WHERE figure_brand_id='".$_GET['brand_id']."'");
		addNotice("success", $locale['figbm_0003']);
	}
	redirect(clean_request("", ["section", "aid"], TRUE));

// Add / Edit a Brand
} else {
	
	// Empty Arrays
	$data = [
		"figure_brand_id"          => 0,
		"figure_brand_name"        => "",
		"figure_brand_description" => "",
		"figure_brand_language"    => LANGUAGE,
		"figure_brand_parent"      => "",
		"figure_brand_sorting"     => "figure_title ASC"
	];
	
	// Edit a Brand?
	if (
		(isset($_GET['action']) && $_GET['action'] == "edit") && 
		(isset($_GET['brand_id']) && isnum($_GET['brand_id'])) && 
		dbcount("(figure_brand_id)", DB_FIGURE_BRANDS, "figure_brand_id='".$_GET['brand_id']."'")
	) {
		$data = dbarray(dbquery("
			SELECT 
				figure_brand_id, figure_brand_name, figure_brand_description,
				figure_brand_language, figure_brand_parent, figure_brand_sorting
			FROM ".DB_FIGURE_BRANDS."
			WHERE figure_brand_id='".$_GET['brand_id']."'
			LIMIT 0,1
		"));
	}
	
	// Handle posted Informations
	if (isset($_POST['save_brand'])) {
		$data = [
			"figure_brand_id"          => form_sanitizer($_POST['figure_brand_id'],          0,        "figure_brand_id"),
			"figure_brand_name"        => form_sanitizer($_POST['figure_brand_name'],        "",       "figure_brand_name"),
			"figure_brand_description" => form_sanitizer($_POST['figure_brand_description'], "",       "figure_brand_description"),
			"figure_brand_language"    => form_sanitizer($_POST['figure_brand_language'],    LANGUAGE, "figure_brand_language"),
			"figure_brand_parent"      => form_sanitizer($_POST['figure_brand_parent'],      0,        "figure_brand_parent")
		];
		
		// Check Sorting Fields
		$brandSortBy    = form_sanitizer($_POST['brand_sort_by'],    2,     "brand_sort_by");
		$brandSortOrder = form_sanitizer($_POST['brand_sort_order'], "ASC", "brand_sort_order");
		
		// Handle Sorting Informations
		if (isnum($brandSortBy) && $brandSortBy == "1") {
			$data['figure_brand_sorting'] = "figure_id ".($brandSortOrder == "ASC" ? "ASC" : "DESC");
		} elseif (isnum($brandSortBy) && $brandSortBy == "2") {
			$data['figure_brand_sorting'] = "figure_title ".($brandSortOrder == "ASC" ? "ASC" : "DESC");
		} elseif (isnum($brandSortBy) && $brandSortBy == "3") {
			$data['figure_brand_sorting'] = "figure_datestamp ".($brandSortOrder == "ASC" ? "ASC" : "DESC");
		} else {
			$data['figure_brand_sorting'] = "figure_title ASC";
		}
		
		// Handle Brand Check
		$brandNameCheck = [
			"when_updating" => "figure_brand_name='".$data['figure_brand_name']."' AND figure_brand_id != '".$data['figure_brand_id']."'",
			"when_saving"   => "figure_brand_name='".$data['figure_brand_name']."'"
		];
		
		// Handle Action
		if (defender::safe()) {
			
			// Update a Figure
			if ($data['figure_brand_id']) {
				if (!dbcount("(figure_brand_id)", DB_FIGURE_BRANDS, $brandNameCheck['when_updating'])) {
					dbquery_insert(DB_FIGURE_BRANDS, $data, "update");
					addNotice("success", $locale['figbm_0002']);
					redirect(clean_request("", ["section", "aid"], true));
				} else {
					$defender->stop();
					addNotice("danger", $locale['figbm_0006']);
				}
				
			// Save a Figure 
			} else {
				if (!dbcount("(figure_brand_id)", DB_FIGURE_BRANDS, $brandNameCheck['when_saving'])) {
					dbquery_insert(DB_FIGURE_BRANDS, $data, "save");
					addNotice("success", $locale['figbm_0001']);
					redirect(clean_request("", ["section", "aid"], true));
				} else {
					$defender->stop();
					addNotice("danger", $locale['figbm_0006']);
				}
			}
		}
	}
	
	// Split Sorting Field
	$sorting = explode(" ", $data['figure_brand_sorting']);
		
	// Handle Sorting Informations
	if ($sorting[0] == "1") {
		$brandSortBy = "figure_id";
	} elseif ($sorting[0] == "2") {
		$brandSortBy = "figure_title";
	} elseif ($sorting[0] == "3") {
		$brandSortBy = "figure_datestamp";
	} else {
		$brandSortBy = "figure_title";
	}
	$brandSortOrder = $sorting[1];

	// Handle Tab Informations
	$fiBrandTab['title'] = [$locale['figbrand_0010'], $locale['figure_010']]; 
	$fiBrandTab['id']    = ["a", "b"];
	$tab_active          = tab_active($fiBrandTab, isset($_GET['brand_view']) ? 1 : 0);
	
	// Display Tabs
	echo opentab($fiBrandTab, $tab_active, "fiBrand_Tab", false, "m-t-20");
	echo opentabbody($fiBrandTab['title'][0], $fiBrandTab['id'][0], $tab_active);
	
	// Open Form
	echo openform("addbrand", "post", FUSION_REQUEST, ["class" => "m-t-20"]);
	echo form_hidden("figure_brand_id", "", $data['figure_brand_id']);

	// Brand Name
	echo form_text("figure_brand_name", $locale['figbrand_0000'], $data['figure_brand_name'], [
		"inline"     => true,
		"required"   => true,
		"error_text" => $locale['figbrand_0001']
	]);
	
	// Brand Description							 
	echo form_textarea("figure_brand_description", $locale['figbrand_0002'], $data['figure_brand_description'], [
		"inline"    => true,
		"html"      => true,
		"preview"   => false,
		"autosize"  => true,
		"form_name" => "addbrand"
	]);
	
	// Brand Parent
	echo form_select_tree("figure_brand_parent", $locale['figbrand_0003'], $data['figure_brand_parent'], [
		"disable_opts"  => [$data['figure_brand_id']],
		"hide_disabled" => true,
		"inline"        => true,
	], DB_FIGURE_BRANDS, "figure_brand_name", "figure_brand_id", "figure_brand_parent");
	
	// Brand Language
	if (multilang_table("FI")) {
		echo form_select("figure_brand_language", $locale['global_ML100'], $data['figure_brand_language'], [
			"options" => fusion_get_enabled_languages(),
			"inline" => true,
		]);
	} else {
		echo form_hidden("figure_brand_language", "", $data['figure_brand_language']);
	}
	
	// Brand Sorting
	echo "<div class='row m-0'>\n";	
	echo "<label class='label-control col-xs-12 col-sm-3 p-l-0'>".$locale['figbrand_0004']."</label>\n";
	echo "<div class='col-xs-12 col-sm-3 p-l-0'>\n";

		// Brand Sorting By
		echo form_select("brand_sort_by", "", $brandSortBy, [
			"inline"  => true,
			"width"   => "100%",
			"options" => ["1" => $locale['figbrand_0005'], "2" => $locale['figbrand_0006'], "3" => $locale['figbrand_0007']],
			"class"   => "pull-left m-r-10"
		]);
		
	echo "</div>\n";
	echo "<div class='col-xs-12 col-sm-3'>\n";
	
		// Brand Sorting Direction
		echo form_select("brand_sort_order", "", $brandSortOrder, [
			"inline"  => true,
			"width"   => "100%",
			"options" => ["ASC" => $locale['figbrand_0008'], "DESC" => $locale['figbrand_0009']]
		]);
		
	echo "</div>\n";
	echo "</div>\n";
		
	// Button
	echo form_button("save_brand", $locale['figbrand_0011'], $locale['figbrand_0011'], ["class" => "btn-primary m-t-10"]);
	
	// Close Form
	echo closeform();
	
	// Display Tabs
	echo closetabbody();
	
	// Display Tabs
	echo opentabbody($fiBrandTab['title'][1], $fiBrandTab['id'][1], $tab_active);
	figures_showbrandlist();
	echo closetabbody();
	echo closetab();

}

/////////////////////////////////////////////////////////
// FUNCTIONS ////////////////////////////////////////////
/////////////////////////////////////////////////////////
function figures_showbrandlist($parent = 0, $level = 0) {
	global $locale, $aidlink;
	
	// Check Page (only on Level 0)
	if ($parent == "0" && $level == "0") {
		$allRows = dbcount("(figure_brand_id)", DB_FIGURE_BRANDS, "figure_brand_parent='0'".(multilang_table("FI") ? " AND figure_brand_language='".LANGUAGE."'" : "")."");
		if (isset($_GET['rowstart']) && isNum($_GET['rowstart']) && $_GET['rowstart'] <= $allRows) {
			$rowstart = $_GET['rowstart'];
		} else {
			$rowstart = 0;
		}
	}
	
	// Get all Category
	$result = dbquery("
		SELECT 
			figure_brand_id, 
			figure_brand_name, 
			figure_brand_description 
		FROM ".DB_FIGURE_BRANDS." 
		WHERE figure_brand_parent='".$parent."'".(multilang_table("FI") ? " AND figure_brand_language='".LANGUAGE."'" : "")." 
		ORDER BY figure_brand_name ASC
		".($parent == "0" && $level == "0" ? "LIMIT ".$rowstart.",10" : "")."
	");
	
	// If there are Results, display it.
	if (dbrows($result) != 0) {
		if ($parent == "0" && $level == "0") { echo "<table class='table table-responsive table-hover table-striped'>\n"; }
		
		// Display Items
		while ($data = dbarray($result)) {
			$description = strip_tags(parse_textarea($data['figure_brand_description']));
			
			// Display each Item
			echo "<tr>\n";
				echo "<td><strong>".str_repeat("&mdash;", $level).$data['figure_brand_name']."</strong>\n";
				if ($data['figure_brand_description']) {
					echo "<br />".str_repeat("&mdash;", $level)."<span class='small'>".$description."</span>\n";
				}
				echo "</td>\n";
				echo "<td align='center' width='1%' style='white-space:nowrap'>\n";
					echo "<a href='".FUSION_SELF.$aidlink."&amp;section=figurelib_brands&amp;action=edit&amp;brand_id=".$data['figure_brand_id']."' title='".$locale['cifg_0005']."' class='btn btn-warning btn-xs'><i class='fa fa-fw fa-cogs'></i> ".$locale['cifg_0005']."</a>\n"; 
					echo "<a href='".FUSION_SELF.$aidlink."&amp;section=figurelib_brands&amp;action=delete&amp;brand_id=".$data['figure_brand_id']."' title='".$locale['cifg_0006']."' onclick=\"return confirm('".$locale['figbm_0007']."');\" class='btn btn-danger btn-xs'><i class='fa fa-fw fa-trash'></i> ".$locale['cifg_0006']."</a>\n";
				echo "</td>\n";
			echo "</tr>\n";
			
			// Display Sublevel
			figures_showbrandlist($data['figure_brand_id'], $level+1);
		}
		if ($parent == "0" && $level == "0") { echo "</table>\n"; }
		
		// Display Navigation (only on Level 0)
		if ($parent == "0" && $level == "0") {
			if ($allRows > 10) {
				echo "<hr />\n";
				echo "<div class='text-center'>\n";
					echo makepagenav($rowstart, 10, $allRows, 6, FUSION_SELF.$aidlink."&amp;section=figurelib_brands&amp;brand_view&amp;");
				echo "\n</div>\n";
			}
		}
		
	// Otherwise display Message
	} else {
		if ($parent == "0" && $level == "0") {
			echo "<div class='text-center tbl1'>".$locale['figb_0012']."</div>\n";
		}
	}
}

?>