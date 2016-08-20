<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: figurelib_manufacturer based on weblink_cats.php
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

if ((isset($_GET['action']) && $_GET['action'] == "delete") && (isset($_GET['man_id']) && isnum($_GET['man_id']))) {
	
	$result = dbcount("(figure_manufacturer)", DB_FIGURE_ITEMS, "figure_manufacturer='".$_GET['man_id']."'") || dbcount("(figure_manufacturer_id)", DB_FIGURE_MANUFACTURERS, "figure_manufacturer_parent='".$_GET['man_id']."'");
		
	// ['figmm_0004'] = "Figure Manufacturer cannot be deleted"; |
	// ['figmm_0005'] = "There are Figure or Sub-Manufacturer linked to this Manufacturer";
	if (!empty($result)) {
		addNotice("danger", $locale['figmm_0004'].$locale['figmm_0005']); 
		redirect(clean_request("", array("section", "aid"), TRUE));
	} else {
		$result = dbquery("DELETE FROM ".DB_FIGURE_MANUFACTURERS." WHERE figure_manufacturer_id='".$_GET['man_id']."'");
		addNotice("success", $locale['figmm_0003']); // ['figmm_0003'] = "Figure Manufacturer deleted";
		redirect(clean_request("", array("section", "aid"), TRUE));
	}
} else {
	$man_hidden = array();
	$data = array(
		"figure_manufacturer_id" => 0,
		"figure_manufacturer_name" => "",
		"figure_manufacturer_description" => "",
		"figure_manufacturer_language" => LANGUAGE,
		"figure_manufacturer_parent" => "",
		"man_sort_by" => 2,
		"man_sort_order" => "ASC",
	);
	if (isset($_POST['save_man'])) {
		$data = array(
			"figure_manufacturer_id" => form_sanitizer($_POST['figure_manufacturer_id'], 0, 'figure_manufacturer_id'),
			"figure_manufacturer_name" => form_sanitizer($_POST['figure_manufacturer_name'], '', 'figure_manufacturer_name'),
			"figure_manufacturer_description" => form_sanitizer($_POST['figure_manufacturer_description'], '', 'figure_manufacturer_description'),
			"figure_manufacturer_language" => form_sanitizer($_POST['figure_manufacturer_language'], LANGUAGE, 'figure_manufacturer_language'),
			"figure_manufacturer_parent" => form_sanitizer($_POST['figure_manufacturer_parent'], 0, 'figure_manufacturer_parent'),
		);
		$data['man_sort_by'] = form_sanitizer($_POST['man_sort_by'], 2, "man_sort_by");
		$data['man_sort_order'] = form_sanitizer($_POST['man_sort_order'], "ASC", "man_sort_order");
		if (isnum($data['man_sort_by']) && $data['man_sort_by'] == "1") {
			$data['figure_manufacturer_sorting'] = "figure_id ".($data['man_sort_order'] == "ASC" ? "ASC" : "DESC");
		} else if (isnum($_POST['man_sort_by']) && $data['man_sort_by'] == "2") {
			$data['figure_manufacturer_sorting'] = "figure_title ".($data['man_sort_order'] == "ASC" ? "ASC" : "DESC");
		} else if (isnum($_POST['man_sort_by']) && $data['man_sort_by'] == "3") {
			$data['figure_manufacturer_sorting'] = "figure_datestamp ".($data['man_sort_order'] == "ASC" ? "ASC" : "DESC");
		} else {
			$data['figure_manufacturer_sorting'] = "figure_manufacturer_name ASC";
		}
		$manufacturerNameCheck = array(
			"when_updating" => "figure_manufacturer_name='".$data['figure_manufacturer_name']."' and figure_manufacturer_id !='".$data['figure_manufacturer_id']."'",
			"when_saving" => "figure_manufacturer_name='".$data['figure_manufacturer_name']."'",
		);
		if (defender::safe()) {
			if ($figureMan_edit && dbcount("(figure_manufacturer_id)", DB_FIGURE_MANUFACTURERS, "figure_manufacturer_id='".intval($data['figure_manufacturer_id'])."'")) {
				if (!dbcount("(figure_manufacturer_id)", DB_FIGURE_MANUFACTURERS, $manufacturerNameCheck['when_updating'])) {
					dbquery_insert(DB_FIGURE_MANUFACTURERS, $data, "update");
					addNotice("success", $locale['figmm_0002']); // ['figmm_0002'] = "Figure Manufacturer updated";
					redirect(clean_request("", array("section", "aid"), TRUE));
				} else {
					$defender->stop();
					addNotice("danger", $locale['figmm_0006']); // ['figmm_0006'] = "This Manufacturer already exists.";
				}
			} else {
				if (!dbcount("(figure_manufacturer_id)", DB_FIGURE_MANUFACTURERS, $manufacturerNameCheck['when_saving'])) {
					dbquery_insert(DB_FIGURE_MANUFACTURERS, $data, "save");
					addNotice("success", $locale['figmm_0001']); // ['figmm_0001'] = "Figure Manufacturer added";
					redirect(clean_request("", array("section", "aid"), TRUE));
				} else {
					$defender->stop();
					addNotice("danger", $locale['figmm_0006']); // ['figmm_0006'] = "This Manufacturer already exists.";
				}
			}
		}
	}
	if ($figureMan_edit) {
		$result = dbquery("SELECT * FROM ".DB_FIGURE_MANUFACTURERS." ".(multilang_table("FI") ? "WHERE figure_manufacturer_language='".LANGUAGE."' AND" : "WHERE")." figure_manufacturer_id='".intval($_GET['man_id'])."'");
		if (dbrows($result)) {
			$data = dbarray($result);
			$man_hidden = array($data['figure_manufacturer_id']);
			$man_sorting = explode(" ", $data['figure_manufacturer_sorting']);
			if ($man_sorting[0] == "figure_id") {
				$data['man_sort_by'] = "1";
			} elseif ($man_sorting[0] == "figure_title") {
				$data['man_sort_by'] = "2";
			} else {
				$data['man_sort_by'] = "3";
			}
			$data['man_sort_order'] = $man_sorting[1];
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	}
	// ['figm_0010'] = "Figure Manufacturer Editor";
	// ['filt_0014'] = "Figures Manufacturer";
	$fiManTab['title'] = array($locale['figm_0010'], $locale['filt_0014']); 
	$fiManTab['id'] = array("a", "b");
	$tab_active = tab_active($fiManTab, isset($_GET['man_view']) ? 1 : 0);
	echo opentab($fiManTab, $tab_active, "fiMan_Tab", FALSE, "m-t-20");
	echo opentabbody($fiManTab['title'][0], $fiManTab['id'][0], $tab_active);
	echo openform('addman', 'post', FUSION_REQUEST, array("class" => "m-t-20"));
	echo form_hidden("figure_manufacturer_id", "", $data['figure_manufacturer_id']);
	
	// ['figm_0000'] = "Manufacturer Name:";
	// ['figm_0001'] = "Please enter a Manufacturer name";
	echo form_text('figure_manufacturer_name', $locale['figm_0000'], $data['figure_manufacturer_name'], array(
										 'required' => TRUE,
										 "error_text" => $locale['figm_0001'],
										 "inline" => TRUE,
									 ));
	
	// ['figm_0002'] = "Manufacturer Description:";								 
	echo form_textarea('figure_manufacturer_description', $locale['figm_0002'], $data['figure_manufacturer_description'], array(
										"html" => TRUE,
										"preview" => FALSE,
										"autosize" => TRUE,
										"inline" => TRUE,
										"form_name" => "addman"
									));
	// ['figm_0003'] = "Parent Manufacturer";
	echo form_select_tree("figure_manufacturer_parent", $locale['figm_0003'], $data['figure_manufacturer_parent'], array(
										"disable_opts" => $man_hidden,
										"hide_disabled" => TRUE,
										"inline" => TRUE,
										), DB_FIGURE_MANUFACTURERS, "figure_manufacturer_name", "figure_manufacturer_id", "figure_manufacturer_parent");
	
	if (multilang_table("FI")) {
		echo form_select('figure_manufacturer_language', $locale['global_ML100'], $data['figure_manufacturer_language'], array(
												   'options' => fusion_get_enabled_languages(),
												   "inline" => TRUE,
											   ));
	} else {
		echo form_hidden('figure_manufacturer_language', '', $data['figure_manufacturer_language']);
	}
	
	
	echo "<div class='row m-0'>\n";	
	echo "<label class='label-control col-xs-12 col-sm-3 p-l-0'>".$locale['figm_0004']."</label>\n"; // ['figm_0004'] = "Manufacturer Sorting:";
		
	echo "<div class='col-xs-12 col-sm-3  p-l-0'>\n";
	
	// ['figm_0005'] = "Manufacturer ID"; 
	// ['figm_0006'] = "Manufacturer Name"; 
	// ['figm_0007'] = "Manufacturer Date";
	echo form_select('man_sort_by', "", $data['man_sort_by'], array(
		"inline" => TRUE,
		"width" => "100%",
		'options' => array('1' => $locale['figm_0005'], '2' => $locale['figm_0006'], '3' => $locale['figm_0007']),
		'class' => 'pull-left m-r-10'
	));
	echo "</div>\n";
	echo "<div class='col-xs-12 col-sm-2'>\n";
	
	// ['figm_0008'] = "Ascending"; 
	// ['figm_0009'] = "Descending";
	echo form_select('man_sort_order', '', $data['man_sort_order'], array(
		"inline" => TRUE,
		"width" => "100%",
		'options' => array('ASC' => $locale['figm_0008'], 'DESC' => $locale['figm_0009']),
	));
	echo "</div>\n";
	echo "</div>\n";
	
		
	// ['figm_0011'] = "Save Manufacturer";
	echo form_button('save_man', $locale['figm_0011'], $locale['figm_0011'], array('class' => 'btn-primary m-t-10'));
	echo closeform();
	echo closetabbody();
	echo opentabbody($fiManTab['title'][1], $fiManTab['id'][1], $tab_active);
	$row_num = 0;
	echo "<table class='table table-responsive table-hover table-striped'>\n";
	showmanlist();
	if ($row_num == 0) {
		echo "<tr><td align='center' class='tbl1'>".$locale['figm_0012']."</td></tr>\n"; // ['figm_0012'] = "No figure categories defined";
	}
	echo "</table>\n";
	echo closetabbody();
	echo closetab();
}

/////////////////////////////////////////////////////////
// FUNCTIONS ////////////////////////////////////////////
/////////////////////////////////////////////////////////

function showmanlist($parent = 0, $level = 0) {
	global $locale, $aidlink, $row_num;
		$result = dbquery("
			SELECT 
				figure_manufacturer_id, 
				figure_manufacturer_name, 
				figure_manufacturer_description 
			FROM ".DB_FIGURE_MANUFACTURERS." 
			WHERE figure_manufacturer_parent='".$parent."'".(multilang_table("FI") ? " 
			AND figure_manufacturer_language='".LANGUAGE."'" : "")." 
			ORDER BY figure_manufacturer_name
		");
	if (dbrows($result) != 0) {
		while ($data = dbarray($result)) {
			$description = strip_tags(parse_textarea($data['figure_manufacturer_description']));
			echo "<tr>\n";
			echo "<td><strong>".str_repeat("&mdash;", $level).$data['figure_manufacturer_name']."</strong>\n";
			if ($data['figure_manufacturer_description']) {
				echo "<br />".str_repeat("&mdash;", $level)."<span class='small'>".$description."</span></td>\n";
			}
			// ['cifg_0005'] = "Edit";
			echo "<td align='center' width='1%' style='white-space:nowrap'>\n
			<a href='".FUSION_SELF.$aidlink."&amp;section=figurelib_manufacturers&amp;action=edit&amp;man_id=".$data['figure_manufacturer_id']."'>".$locale['cifg_0005']."</a> -\n"; 
			
			// ['figmm_0007'] = "Delete this Manufacturer?";
			// ['cifg_0006'] = "Delete";
			echo "<a href='".FUSION_SELF.$aidlink."&amp;section=figurelib_manufacturers&amp;action=delete&amp;man_id=".$data['figure_manufacturer_id']."' onclick=\"return confirm('".$locale['figmm_0007']."');\">".$locale['cifg_0006']."</a></td>\n";
			echo "</tr>\n";
			$row_num++;
			showmanlist($data['figure_manufacturer_id'], $level+1);
		}
	}
}
