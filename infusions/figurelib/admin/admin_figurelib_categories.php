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
pageAccess("FI");
if ((isset($_GET['action']) && $_GET['action'] == "delete") && (isset($_GET['cat_id']) && isnum($_GET['cat_id']))) {
	
	$result = dbcount("(figure_cat)", DB_FIGURE_ITEMS, "figure_cat='".$_GET['cat_id']."'") || dbcount("(figure_cat_id)", DB_FIGURE_CATS, "figure_cat_parent='".$_GET['cat_id']."'");
		
	// ['figcm_0004'] = "Figure Category cannot be deleted"; |
	// ['figcm_0005'] = "There are Figure or Sub-Categories linked to this category";
	if (!empty($result)) {
		addNotice("danger", $locale['figcm_0004'].$locale['figcm_0005']); 
		redirect(clean_request("", array("section", "aid"), TRUE));
	} else {
		$result = dbquery("DELETE FROM ".DB_FIGURE_CATS." WHERE figure_cat_id='".$_GET['cat_id']."'");
		addNotice("success", $locale['figcm_0003']); // ['figcm_0003'] = "Figure Category deleted";
		redirect(clean_request("", array("section", "aid"), TRUE));
	}
} else {
	$cat_hidden = array();
	$data = array(
		"figure_cat_id" => 0,
		"figure_cat_name" => "",
		"figure_cat_description" => "",
		"figure_cat_language" => LANGUAGE,
		"figure_cat_parent" => "",
		"cat_sort_by" => 2,
		"cat_sort_order" => "ASC",
	);
	if (isset($_POST['save_cat'])) {
		$data = array(
			"figure_cat_id" => form_sanitizer($_POST['figure_cat_id'], 0, 'figure_cat_id'),
			"figure_cat_name" => form_sanitizer($_POST['figure_cat_name'], '', 'figure_cat_name'),
			"figure_cat_description" => form_sanitizer($_POST['figure_cat_description'], '', 'figure_cat_description'),
			"figure_cat_language" => form_sanitizer($_POST['figure_cat_language'], LANGUAGE, 'figure_cat_language'),
			"figure_cat_parent" => form_sanitizer($_POST['figure_cat_parent'], 0, 'figure_cat_parent'),
		);
		$data['cat_sort_by'] = form_sanitizer($_POST['cat_sort_by'], 2, "cat_sort_by");
		$data['cat_sort_order'] = form_sanitizer($_POST['cat_sort_order'], "ASC", "cat_sort_order");
		if (isnum($data['cat_sort_by']) && $data['cat_sort_by'] == "1") {
			$data['figure_cat_sorting'] = "figure_id ".($data['cat_sort_order'] == "ASC" ? "ASC" : "DESC");
		} else if (isnum($_POST['cat_sort_by']) && $data['cat_sort_by'] == "2") {
			$data['figure_cat_sorting'] = "figure_title ".($data['cat_sort_order'] == "ASC" ? "ASC" : "DESC");
		} else if (isnum($_POST['cat_sort_by']) && $data['cat_sort_by'] == "3") {
			$data['figure_cat_sorting'] = "figure_datestamp ".($data['cat_sort_order'] == "ASC" ? "ASC" : "DESC");
		} else {
			$data['figure_cat_sorting'] = "figure_title ASC";
		}
		$categoryNameCheck = array(
			"when_updating" => "figure_cat_name='".$data['figure_cat_name']."' and figure_cat_id !='".$data['figure_cat_id']."'",
			"when_saving" => "figure_cat_name='".$data['figure_cat_name']."'",
		);
		if (defender::safe()) {
			if ($figureCat_edit && dbcount("(figure_cat_id)", DB_FIGURE_CATS, "figure_cat_id='".intval($data['figure_cat_id'])."'")) {
				if (!dbcount("(figure_cat_id)", DB_FIGURE_CATS, $categoryNameCheck['when_updating'])) {
					dbquery_insert(DB_FIGURE_CATS, $data, "update");
					addNotice("success", $locale['figcm_0002']); // ['figcm_0002'] = "Figure Category updated";
					redirect(clean_request("", array("section", "aid"), TRUE));
				} else {
					$defender->stop();
					addNotice("danger", $locale['figcm_0006']); // ['figcm_0006'] = "This category already exists.";
				}
			} else {
				if (!dbcount("(figure_cat_id)", DB_FIGURE_CATS, $categoryNameCheck['when_saving'])) {
					dbquery_insert(DB_FIGURE_CATS, $data, "save");
					addNotice("success", $locale['figcm_0001']); // ['figcm_0001'] = "Figure Category added";
					redirect(clean_request("", array("section", "aid"), TRUE));
				} else {
					$defender->stop();
					addNotice("danger", $locale['figcm_0006']); // ['figcm_0006'] = "This category already exists.";
				}
			}
		}
	}
	if ($figureCat_edit) {
		$result = dbquery("SELECT * FROM ".DB_FIGURE_CATS." ".(multilang_table("FI") ? "WHERE figure_cat_language='".LANGUAGE."' AND" : "WHERE")." figure_cat_id='".intval($_GET['cat_id'])."'");
		if (dbrows($result)) {
			$data = dbarray($result);
			$cat_hidden = array($data['figure_cat_id']);
			$cat_sorting = explode(" ", $data['figure_cat_sorting']);
			if ($cat_sorting[0] == "figure_id") {
				$data['cat_sort_by'] = "1";
			} elseif ($cat_sorting[0] == "figure_title") {
				$data['cat_sort_by'] = "2";
			} else {
				$data['cat_sort_by'] = "3";
			}
			$data['cat_sort_order'] = $cat_sorting[1];
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	}
	// ['figc_0010'] = "Figure Category Editor";
	// ['filt_0004'] = "Figures Categories";
	$fiCatTab['title'] = array($locale['figc_0010'], $locale['filt_0004']); 
	$fiCatTab['id'] = array("a", "b");
	$tab_active = tab_active($fiCatTab, isset($_GET['cat_view']) ? 1 : 0);
	echo opentab($fiCatTab, $tab_active, "fiCat_Tab", FALSE, "m-t-20");
	echo opentabbody($fiCatTab['title'][0], $fiCatTab['id'][0], $tab_active);
	echo openform('addcat', 'post', FUSION_REQUEST, array("class" => "m-t-20"));
	echo form_hidden("figure_cat_id", "", $data['figure_cat_id']);
	
	// ['figc_0000'] = "Category Name:";
	// ['figc_0001'] = "Please enter a category name";
	echo form_text('figure_cat_name', $locale['figc_0000'], $data['figure_cat_name'], array(
										 'required' => TRUE,
										 "error_text" => $locale['figc_0001'],
										 "inline" => TRUE,
									 ));
	
	// ['figc_0002'] = "Category Description:";								 
	echo form_textarea('figure_cat_description', $locale['figc_0002'], $data['figure_cat_description'], array(
										"html" => TRUE,
										"preview" => FALSE,
										"autosize" => TRUE,
										"inline" => TRUE,
										"form_name" => "addcat"
									));
	// ['figc_0003'] = "Parent Category";
	echo form_select_tree("figure_cat_parent", $locale['figc_0003'], $data['figure_cat_parent'], array(
										"disable_opts" => $cat_hidden,
										"hide_disabled" => TRUE,
										"inline" => TRUE,
										), DB_FIGURE_CATS, "figure_cat_name", "figure_cat_id", "figure_cat_parent");
	
	if (multilang_table("FI")) {
		echo form_select('figure_cat_language', $locale['global_ML100'], $data['figure_cat_language'], array(
												   'options' => fusion_get_enabled_languages(),
												   "inline" => TRUE,
											   ));
	} else {
		echo form_hidden('figure_cat_language', '', $data['figure_cat_language']);
	}
	
	echo "<div class='row m-0'>\n";
	
	echo "<label class='label-control col-xs-12 col-sm-3 p-l-0'>".$locale['figc_0004']."</label>\n"; // ['figc_0004'] = "Category Sorting:";

	echo "<div class='col-xs-12 col-sm-3  p-l-0'>\n";
	
	// ['figc_0005'] = "Figure ID"; 
	// ['figc_0006'] = "Figure Name";
	// ['figc_0007'] = "Figure Date";
	echo form_select('cat_sort_by', "", $data['cat_sort_by'], array(
		"inline" => TRUE,
		"width" => "100%",
		'options' => array('1' => $locale['figc_0005'], '2' => $locale['figc_0006'], '3' => $locale['figc_0007']),
		'class' => 'pull-left m-r-10'
	));
	echo "</div>\n";
	echo "<div class='col-xs-12 col-sm-2'>\n";
	
	// ['figc_0008'] = "Ascending"; 
	// ['figc_0009'] = "Descending";
	echo form_select('cat_sort_order', '', $data['cat_sort_order'], array(
		"inline" => TRUE,
		"width" => "100%",
		'options' => array('ASC' => $locale['figc_0008'], 'DESC' => $locale['figc_0009']),
	));
	echo "</div>\n";
	echo "</div>\n";

	
	
	// ['figc_0011'] = "Save Category";
	echo form_button('save_cat', $locale['figc_0011'], $locale['figc_0011'], array('class' => 'btn-primary m-t-10'));
	echo closeform();
	echo closetabbody();
	echo opentabbody($fiCatTab['title'][1], $fiCatTab['id'][1], $tab_active);
	$row_num = 0;
	echo "<table class='table table-responsive table-hover table-striped'>\n";
	showcatlist();
	if ($row_num == 0) {
		echo "<tr><td align='center' class='tbl1'>".$locale['figc_0012']."</td></tr>\n"; // ['figc_0012'] = "No figure categories defined";
	}
	echo "</table>\n";
	echo closetabbody();
	echo closetab();
}
/////////////////////////////////////////////////////////
// FUNCTIONS ////////////////////////////////////////////
/////////////////////////////////////////////////////////

function showcatlist($parent = 0, $level = 0) {
	global $locale, $aidlink, $row_num;
		$result = dbquery("
			SELECT 
				figure_cat_id, 
				figure_cat_name, 
				figure_cat_description 
			FROM ".DB_FIGURE_CATS." 
			WHERE figure_cat_parent='".$parent."'".(multilang_table("FI") ? " 
			AND figure_cat_language='".LANGUAGE."'" : "")." 
			ORDER BY figure_cat_name
		");
	
	if (dbrows($result) != 0) {
		while ($data = dbarray($result)) {
			$description = strip_tags(parse_textarea($data['figure_cat_description']));
			echo "<tr>\n";
			echo "<td><strong>".str_repeat("&mdash;", $level).$data['figure_cat_name']."</strong>\n";
			if ($data['figure_cat_description']) {
				echo "<br />".str_repeat("&mdash;", $level)."<span class='small'>".$description."</span></td>\n";
			}
			// ['cifg_0005'] = "Edit";
			echo "<td align='center' width='1%' style='white-space:nowrap'>\n
			<a href='".FUSION_SELF.$aidlink."&amp;section=figurelib_categories&amp;action=edit&amp;cat_id=".$data['figure_cat_id']."'>".$locale['cifg_0005']."</a> -\n"; 
			
			// ['figcm_0007'] = "Delete this Figure category?"; / ['cifg_0006'] = "Delete";
			echo "<a href='".FUSION_SELF.$aidlink."&amp;section=figurelib_categories&amp;action=delete&amp;cat_id=".$data['figure_cat_id']."' onclick=\"return confirm('".$locale['figcm_0007']."');\">".$locale['cifg_0006']."</a></td>\n";
			echo "</tr>\n";
			$row_num++;
			showcatlist($data['figure_cat_id'], $level+1);
		}
	}
}
