<?php

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
// MANUFACTURER OVERVIEW
// ******************************************************************************************

if (!function_exists('render_manufacturer')) {
	function render_manufacturer($info) {
		global $locale;
		
		// Display Breadcrumbs
		echo render_breadcrumbs();

		// Display Content
		echo "<aside class='list-group-item m-b-20'>\n";
		
		// If there Items, display it
		if ($info['rows'] != 0) {
			
			// Counters
			$counter = 0; $columns = 2;
			
			// Start Display
			echo "<div class='row m-0'>\n";
			if (!empty($info['items'])) {
				foreach($info['items'] as $item) {
					if ($counter != 0 && ($counter%$columns == 0)) {
						echo "</div>\n<div class='row m-0'>\n";
					}
					echo "<div class='col-xs-12 col-sm-12 col-md-6 col-lg-6 p-t-20'>\n";
						echo "<div class='media'>\n";
							echo "<div class='pull-left'>".(!$item['manufacturer-image'] ? "<i class='entypo folder mid-opacity icon-sm'></i>" : "<img src='".$item['manufacturer-image']."' alt='Figure Image' style='max-height: 45px;' />")."</div>\n";
							echo "<div class='media-body overflow-hide'>\n";
								echo "<div class='media-heading strong'><a href='".$item['manufacturer-link']."' alt='".$item['manufacturer-title']."' title='".$item['manufacturer-title']."'>".trimlink($item['manufacturer-title'],30)."</a> <span class='small'>[ ".$item['manufacturer-counter']." ]</span></div>\n";
							echo "</div>\n";
						echo "</div>\n";
					echo "</div>\n";
					$counter++;
				}
			}
			echo "</div>\n";
			
		// Display Message
		// $locale['figure_1711'] = "There are no Manufacturers or Figures in this Category!";
		} else {
			echo "<div class='alert alert-info m-b-20 submission-guidelines'>";
			echo "<div style='text-align:center'><br />".$locale['figure_1711']."<br /><br />\n</div>\n";
			echo "</div>\n";
		}
		
		// Display Content
		echo "</aside>\n";
		
	}
}	

// ******************************************************************************************			
// ******************************************************************************************

?>
