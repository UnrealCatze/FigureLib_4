<?php

	//error_reporting(E_ALL);
	// Formularinhalte prüfen
	//print_r ($_POST);
	// GET-Parameter prüfen
	//print_r ($_GET);
	// Sessions prüfen
	//print_r ($_SESSION);

/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: template/template_render_figure_cats.php based on template/weblinks.php
| Author: PHP-Fusion Development Team
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
include INFUSIONS."figurelib/infusion_db.php";
global $aidlink;
global $settings;


// ******************************************************************************************			
// CATEGORY OVERVIEW
// ******************************************************************************************

if (!function_exists('render_figure_cats')) {
	function render_figure_cats($info) {
		global $locale;
		echo render_breadcrumbs();
		
		// ['cifg_0009'] = "Filter by:";
		//opentable($locale['cifg_0009']);
		echo "<aside class='list-group-item m-b-20'>\n";
		
		if ($info['figure_cat_rows'] != 0) {
			$counter = 0;
			$columns = 2;
			echo "<div class='row m-0'>\n";
			if (!empty($info['item'])) {
				foreach($info['item'] as $figure_cat_id => $data) {
					if ($counter != 0 && ($counter%$columns == 0)) {
						echo "</div>\n<div class='row m-0'>\n";
					}
					echo "<div class='col-xs-12 col-sm-6 col-md-6 col-lg-6 p-t-20'>\n";

						echo "<div class='media'>\n";
						echo "<div class='pull-left'><i class='entypo folder mid-opacity icon-sm'></i></div>\n";
						echo "<div class='media-body overflow-hide'>\n";
						echo "<div class='media-heading strong'><a href='".$data['figure_item']['link']."'>".$data['figure_item']['name']."</a> <span class='small'>[ ".$data['figure_anzahl']." ]</span></div>\n";
						if ($data['figure_cat_description'] != "") {
							echo "<span>".$data['figure_cat_description']."</span>";
						}
					echo "</div>\n</div>\n";
					echo "</div>\n";
					$counter++;
				}
			}
			echo "</div>\n";
		} else {
			// ['figc_0012'] = "No figure categories defined";
			echo "<div style='text-align:center'><br />\n".$locale['figc_0012']."<br /><br />\n</div>\n";
		}
		echo "</aside>\n";
	}
}	

// ******************************************************************************************			
// ******************************************************************************************
