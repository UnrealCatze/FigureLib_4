<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2012 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename:  search_figure_include.php based on search_books_include.php
| Author: Robert Gaudyn (Wooya)
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
include INFUSIONS."figurelib/infusion_db.php";
include LOCALE.LOCALESET."search/figurelib.php";
if ($_GET['stype'] == "figurelib" || $_GET['stype'] == "all") {
	if ($_POST['sort'] == "datestamp") {
		$sortby = "figure_datestamp";
	} else if ($_POST['sort'] == "subject") {
		$sortby = "figure_title";
	} else {
		$sortby = "figure_datestamp";
	}
	$ssubject = search_querylike("figure_title");
	$smessage = search_querylike("figure_description");
	if ($_POST['fields'] == 0) {
		$fieldsvar = search_fieldsvar($ssubject);
	} else if ($_POST['fields'] == 1) {
		$fieldsvar = search_fieldsvar($smessage);
	} else if ($_POST['fields'] == 2) {
		$fieldsvar = search_fieldsvar($ssubject, $smessage);
	} else {
		$fieldsvar = "";
	}
	if ($fieldsvar) {
		$result = dbquery("
			SELECT 
				figure_id
			FROM ".DB_FIGURE_ITEMS."
			WHERE ".$fieldsvar." AND figure_freigabe='1' ".($_POST['datelimit'] != 0 ? " AND figure_datestamp >= '".(time() - $_POST['datelimit'])."'" : "")
		);
		$rows = dbrows($result);
	} else {
		$rows = 0;
	}
	if ($rows != 0) {
		$items_count .= THEME_BULLET."&nbsp;<a href='".FUSION_SELF."?stype=figurelib&amp;stext=".$_POST['stext']."&amp;".$composevars."'>".$locale['f401'].$rows."</a><br />\n";
		$result = dbquery("
			SELECT 
				f.figure_id, f.figure_title, f.figure_description, f.figure_datestamp, f.figure_clickcount, f.figure_pubdate,
				fc.figure_cat_name,
				fm.figure_manufacturer_name,
				fu.user_id, fu.user_name, fu.user_status
			FROM ".DB_FIGURE_ITEMS." AS f
			LEFT JOIN ".DB_FIGURE_CATS." AS fc ON fc.figure_cat_id=f.figure_cat
			LEFT JOIN ".DB_FIGURE_MANUFACTURERS." AS fm ON fm.figure_manufacturer_id=f.figure_manufacturer
			LEFT JOIN ".DB_USERS." AS fu ON fu.user_id=f.figure_submitter
			WHERE ".$fieldsvar." AND f.figure_freigabe='1'
			".($_POST['datelimit'] != 0 ? " AND figure_datestamp >= '".(time() - $_POST['datelimit'])."'" : "")."
			ORDER BY ".$sortby." ".($_POST['order'] == 1 ? "ASC" : "DESC").($_GET['stype'] != "all" ? " LIMIT ".$_POST['rowstart'].",10" : "")
		);
		while ($data = dbarray($result)) {
			$search_result = "";
			if ($data['figure_datestamp']+604800 > time()+($settings['timeoffset']*3600)) {
				$new = " <span class='small'>".$locale['f403']."</span>";
			} else {
				$new = "";
			}
			$text_all = $data['figure_description'];
			$text_all = search_striphtmlbbcodes($text_all);
			$text_frag = search_textfrag($text_all);
			$subj_c = search_stringscount($data['figure_title']);
			$text_c = search_stringscount($data['figure_description']);
			$text_frag = highlight_words($swords, $text_frag);
			$search_result .= "[".$data['figure_cat_name']."] <a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."' target='_blank'>".highlight_words($swords, $data['figure_title'])."</a> $new"."<br /><br />\n";
			if ($text_frag != "") $search_result .= "<div class='quote' style='width:auto;height:auto;overflow:auto'>".$text_frag."</div><br />";
			$search_result .= "<span class='small'><span class='alt'>".$locale['f404']."</span> ".($data['user_id'] ? profile_link($data['user_id'], $data['user_name'], $data['user_status']) : "");
			$search_result .= ($data['figure_manufacturer_name'] ? " |\n<span class='alt'>".$locale['f405']."</span> ".$data['figure_manufacturer_name'] : "");
			$search_result .= ($data['figure_pubdate'] ? " |\n<span class='alt'>".$locale['f406']."</span> ".$data['figure_pubdate'] : "")."<br />\n";
			$search_result .= "<span class='alt'>".$locale['f407']."</span> ".showdate("%d.%m.%y", $data['figure_datestamp'])." |\n";
			$search_result .= "<span class='alt'>".$locale['f408']."</span> ".$data['figure_clickcount']."</span><br /><br />\n";
			search_globalarray($search_result);
		}
	} else {
		$items_count .= THEME_BULLET."&nbsp;".$locale['f402']."<br />\n";
	}
	$navigation_result = search_navigation($rows);
}
?>