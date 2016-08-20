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
if ((isset($_GET['action']) && $_GET['action'] == "delete") && (isset($_GET['brand_id']) && isnum($_GET['brand_id']))) {
	
	$result = dbcount("(figure_brand)", DB_FIGURE_ITEMS, "figure_brand='".$_GET['brand_id']."'") || dbcount("(figure_brand_id)", DB_FIGURE_BRANDS, "figure_brand_parent='".$_GET['brand_id']."'");
	
	
	// ['figbm_0004'] = "Figure Brand cannot be deleted"; |
	// ['figbm_0005'] = "There are Figure or Sub-Brand linked to this Brand";
	if (!empty($result)) {
		addNotice("danger", $locale['figbm_0004'].$locale['figbm_0005']); 
		redirect(clean_request("", array("section", "aid"), TRUE));
	} else {
		$result = dbquery("DELETE FROM ".DB_FIGURE_BRANDS." WHERE figure_brand_id='".$_GET['brand_id']."'");
		addNotice("success", $locale['figbm_0003']); // ['figbm_0003'] = "Figure Brand deleted";
		redirect(clean_request("", array("section", "aid"), TRUE));
	}
} else {
	$brand_hidden = array();
	$data = array(
		"figure_brand_id" => 0,
		"figure_brand_name" => "",
		"figure_brand_description" => "",
		"figure_brand_language" => LANGUAGE,
		"figure_brand_parent" => "",
		"brand_sort_by" => 2,
		"brand_sort_order" => "ASC",
	);
	if (isset($_POST['save_brand'])) {
		$data = array(
			"figure_brand_id" => form_sanitizer($_POST['figure_brand_id'], 0, 'figure_brand_id'),
			"figure_brand_name" => form_sanitizer($_POST['figure_brand_name'], '', 'figure_brand_name'),
			"figure_brand_description" => form_sanitizer($_POST['figure_brand_description'], '', 'figure_brand_description'),
			"figure_brand_language" => form_sanitizer($_POST['figure_brand_language'], LANGUAGE, 'figure_brand_language'),
			"figure_brand_parent" => form_sanitizer($_POST['figure_brand_parent'], 0, 'figure_brand_parent'),
		);
		$data['brand_sort_by'] = form_sanitizer($_POST['brand_sort_by'], 2, "brand_sort_by");
		$data['brand_sort_order'] = form_sanitizer($_POST['brand_sort_order'], "ASC", "brand_sort_order");
		if (isnum($data['brand_sort_by']) && $data['brand_sort_by'] == "1") {
			$data['figure_brand_sorting'] = "figure_id ".($data['brand_sort_order'] == "ASC" ? "ASC" : "DESC");
		} else if (isnum($_POST['brand_sort_by']) && $data['brand_sort_by'] == "2") {
			$data['figure_brand_sorting'] = "figure_title ".($data['brand_sort_order'] == "ASC" ? "ASC" : "DESC");
		} else if (isnum($_POST['brand_sort_by']) && $data['brand_sort_by'] == "3") {
			$data['figure_brand_sorting'] = "figure_datestamp ".($data['brand_sort_order'] == "ASC" ? "ASC" : "DESC");
		} else {
			$data['figure_brand_sorting'] = "figure_brand_name ASC";
		}
		$brandNameCheck = array(
			"when_updating" => "figure_brand_name='".$data['figure_brand_name']."' and figure_brand_id !='".$data['figure_brand_id']."'",
			"when_saving" => "figure_brand_name='".$data['figure_brand_name']."'",
		);
		if (defender::safe()) {
			if ($figureBrand_edit && dbcount("(figure_brand_id)", DB_FIGURE_BRANDS, "figure_brand_id='".intval($data['figure_brand_id'])."'")) {
				if (!dbcount("(figure_brand_id)", DB_FIGURE_BRANDS, $brandNameCheck['when_updating'])) {
					dbquery_insert(DB_FIGURE_BRANDS, $data, "update");
					addNotice("success", $locale['figbm_0002']); // ['figbm_0002'] = "Figure Brand updated";
					redirect(clean_request("", array("section", "aid"), TRUE));
				} else {
					$defender->stop();
					addNotice("danger", $locale['figbm_0006']); // ['figbm_0006'] = "This Brand already exists.";
				}
			} else {
				if (!dbcount("(figure_brand_id)", DB_FIGURE_BRANDS, $brandNameCheck['when_saving'])) {
					dbquery_insert(DB_FIGURE_BRANDS, $data, "save");
					addNotice("success", $locale['figbm_0001']); // ['figbm_0001'] = "Figure Brand added";
					redirect(clean_request("", array("section", "aid"), TRUE));
				} else {
					$defender->stop();
					addNotice("danger", $locale['figbm_0006']); // ['figbm_0006'] = "This Brand already exists.";
				}
			}
		}
	}
	if ($figureBrand_edit) {
		$result = dbquery("SELECT * FROM ".DB_FIGURE_BRANDS." ".(multilang_table("FI") ? "WHERE figure_brand_language='".LANGUAGE."' AND" : "WHERE")." figure_brand_id='".intval($_GET['brand_id'])."'");
		if (dbrows($result)) {
			$data = dbarray($result);
			$brand_hidden = array($data['figure_brand_id']);
			$brand_sorting = explode(" ", $data['figure_brand_sorting']);
			if ($brand_sorting[0] == "figure_id") {
				$data['brand_sort_by'] = "1";
			} elseif ($brand_sorting[0] == "figure_title") {
				$data['brand_sort_by'] = "2";
			} else {
				$data['brand_sort_by'] = "3";
			}
			$data['brand_sort_order'] = $brand_sorting[1];
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	}
	// ['figbrand_0010'] = "Figure Brand Editor";
	// ['figure_010'] = "Figure Brands";
	$fiBrandTab['title'] = array($locale['figbrand_0010'], $locale['figure_010']); 
	$fiBrandTab['id'] = array("a", "b");
	$tab_active = tab_active($fiBrandTab, isset($_GET['brand_view']) ? 1 : 0);
	echo opentab($fiBrandTab, $tab_active, "fiBrand_Tab", FALSE, "m-t-20");
	echo opentabbody($fiBrandTab['title'][0], $fiBrandTab['id'][0], $tab_active);
	echo openform('addbrand', 'post', FUSION_REQUEST, array("class" => "m-t-20"));
	echo form_hidden("figure_brand_id", "", $data['figure_brand_id']);
	
	// ['figbrand_0000'] = "Brand Name:";
	// ['figbrand_0001'] = "Please enter a Brand name";
	echo form_text('figure_brand_name', $locale['figbrand_0000'], $data['figure_brand_name'], array(
										 'required' => TRUE,
										 "error_text" => $locale['figbrand_0001'],
										 "inline" => TRUE,
									 ));
	
	// ['figbrand_0002'] = "Brand Description:";								 
	echo form_textarea('figure_brand_description', $locale['figbrand_0002'], $data['figure_brand_description'], array(
										"html" => TRUE,
										"preview" => FALSE,
										"autosize" => TRUE,
										"inline" => TRUE,
										"form_name" => "addbrand"
									));
	// ['figbrand_0003'] = "Parent Brand";
	echo form_select_tree("figure_brand_parent", $locale['figbrand_0003'], $data['figure_brand_parent'], array(
										"disable_opts" => $brand_hidden,
										"hide_disabled" => TRUE,
										"inline" => TRUE,
										), DB_FIGURE_BRANDS, "figure_brand_name", "figure_brand_id", "figure_brand_parent");
	
	if (multilang_table("FI")) {
		echo form_select('figure_brand_language', $locale['global_ML100'], $data['figure_brand_language'], array(
												   'options' => fusion_get_enabled_languages(),
												   "inline" => TRUE,
											   ));
	} else {
		echo form_hidden('figure_brand_language', '', $data['figure_brand_language']);
	}
		
	echo "<div class='row m-0'>\n";	
	echo "<label class='label-control col-xs-12 col-sm-3 p-l-0'>".$locale['figbrand_0004']."</label>\n"; // ['figbrand_0004'] = "Brand Sorting:";
		
	echo "<div class='col-xs-12 col-sm-3  p-l-0'>\n";
	
	// ['figbrand_0005'] = "Brand ID"; 
	// ['figbrand_0006'] = "Brand Name"; 
	// ['figbrand_0007'] = "Brand Date";
	echo form_select('brand_sort_by', "", $data['brand_sort_by'], array(
		"inline" => TRUE,
		"width" => "100%",
		'options' => array('1' => $locale['figbrand_0005'], '2' => $locale['figbrand_0006'], '3' => $locale['figbrand_0007']),
		'class' => 'pull-left m-r-10'
	));
	echo "</div>\n";
	echo "<div class='col-xs-12 col-sm-2'>\n";
	
	// ['figbrand_0008'] = "Ascending"; 
	// ['figbrand_0009'] = "Descending";
	echo form_select('brand_sort_order', '', $data['brand_sort_order'], array(
		"inline" => TRUE,
		"width" => "100%",
		'options' => array('ASC' => $locale['figbrand_0008'], 'DESC' => $locale['figbrand_0009']),
	));
	echo "</div>\n";
	echo "</div>\n";
		
		
	// ['figbrand_0011'] = "Save Brand";
	echo form_button('save_brand', $locale['figbrand_0011'], $locale['figbrand_0011'], array('class' => 'btn-primary m-t-10'));
	echo closeform();
	echo closetabbody();
	echo opentabbody($fiBrandTab['title'][1], $fiBrandTab['id'][1], $tab_active);
	$row_num = 0;
	echo "<table class='table table-responsive table-hover table-striped'>\n";
	showbrandlist();
	if ($row_num == 0) {
		echo "<tr><td align='center' class='tbl1'>".$locale['figbrand_0012']."</td></tr>\n"; // ['figbrand_0012'] = "No figure categories defined";
	}
	echo "</table>\n";
	echo closetabbody();
	echo closetab();
}

/////////////////////////////////////////////////////////
// FUNCTIONS ////////////////////////////////////////////
/////////////////////////////////////////////////////////

function showbrandlist($parent = 0, $level = 0) {
	global $locale, $aidlink, $row_num;
		$result = dbquery("
			SELECT 
				figure_brand_id, 
				figure_brand_name, 
				figure_brand_description 
			FROM ".DB_FIGURE_BRANDS." 
			WHERE figure_brand_parent='".$parent."'".(multilang_table("FI") ? " 
			AND figure_brand_language='".LANGUAGE."'" : "")." 
			ORDER BY figure_brand_name
		");
	if (dbrows($result) != 0) {
		while ($data = dbarray($result)) {
			$description = strip_tags(parse_textarea($data['figure_brand_description']));
			echo "<tr>\n";
			echo "<td><strong>".str_repeat("&mdash;", $level).$data['figure_brand_name']."</strong>\n";
			if ($data['figure_brand_description']) {
				echo "<br />".str_repeat("&mdash;", $level)."<span class='small'>".$description."</span></td>\n";
			}
			// ['cifg_0005'] = "Edit";
			echo "<td align='center' width='1%' style='white-space:nowrap'>\n
			<a href='".FUSION_SELF.$aidlink."&amp;section=figurelib_brands&amp;action=edit&amp;brand_id=".$data['figure_brand_id']."'>".$locale['cifg_0005']."</a> -\n"; 
			
			// ['figbm_0007'] = "Delete this Brand?";
			// ['cifg_0006'] = "Delete";
			echo "<a href='".FUSION_SELF.$aidlink."&amp;section=figurelib_brands&amp;action=delete&amp;brand_id=".$data['figure_brand_id']."' onclick=\"return confirm('".$locale['figbm_0007']."');\">".$locale['cifg_0006']."</a></td>\n";
			echo "</tr>\n";
			$row_num++;
			showbrandlist($data['figure_brand_id'], $level+1);
		}
	}
}
