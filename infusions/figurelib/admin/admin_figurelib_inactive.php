<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: admin_figurelib_inactive.php based on weblinks_submissions.php
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
	//error_reporting(E_ALL);
	//Formularinhalte prüfen
	//print_r ($_POST);
	//GET-Parameter prüfen
	//print_r ($_GET);
/*--------------------------------------------------------*/

	$result = dbquery("SELECT
			fi.figure_id, fi.figure_datestamp, fi.figure_scale, fi.figure_manufacturer, fi.figure_cat, fi.figure_title, tu.user_id, tu.user_name, tu.user_avatar, tu.user_status
			FROM ".DB_FIGURE_ITEMS." fi
			LEFT JOIN ".DB_USERS." tu ON fi.figure_submitter=tu.user_id
			WHERE figure_freigabe='3' order by figure_datestamp desc
			");
			
	$rows = dbrows($result);
	if ($rows > 0) {
		// ['figs_0021'] = "There are currently %s pending for your review.";
		echo "<div class='well'>".sprintf($locale['figs_0021'], format_word($rows, $locale['fmt_submission']))."</div>\n";
		echo "<table class='table table-striped'>\n";
		echo "<tr>\n";
		
		echo "<th>".$locale['figs_0013']."</th>";	// ['figs_0013'] = "Submission Subject for Review";
		echo "<th>".$locale['cifg_0012']."</th>";	// ['cifg_0012'] = "Category";
		echo "<th>".$locale['cifg_0010']."</th>";	// ['cifg_0010'] = "Manufacturer"; 
		echo "<th>".$locale['cifg_0011']."</th>";	// ['cifg_0011'] = "Scale";	
		echo "<th>".$locale['figs_0014']."</th>";	// ['figs_0014'] = "Submission Author";
		echo "<th>".$locale['figs_0015']."</th>";	// ['figs_0015'] = "Submission Time";
	  //echo "<th>".$locale['cifg_0004']."</th>";	// ['cifg_0004'] = "Figure Id";
			
		echo "</tr>\n";
		echo "<tbody>\n";
	while ($data = dbarray($result)) {
					
			echo "<tr>\n";			
				echo "<td><a href='".FUSION_SELF.$aidlink."&amp;section=figurelib_form&amp;action=edit&amp;figure_id=".$data['figure_id']."'>".$data['figure_title']."</a></td>\n";
			
									$category = dbquery("
										SELECT 
												fc.figure_cat_id,
												fc.figure_cat_name,
												fi.figure_cat
											FROM ".DB_FIGURE_CATS." fc
											INNER JOIN ".DB_FIGURE_ITEMS." fi ON fc.figure_cat_id = fi.figure_cat
											WHERE figure_id='".$data['figure_id']."' 
											");				
									if(dbrows($category)){
									while($datacategory = dbarray($category)){
				echo "<td>".trimlink($datacategory['figure_cat_name'],10)."</td>\n";
									}}	
			
									$manufacturer = dbquery("
										SELECT 
												fm.figure_manufacturer_id,
												fm.figure_manufacturer_name,
												fi.figure_manufacturer
											FROM ".DB_FIGURE_MANUFACTURERS." fm
											INNER JOIN ".DB_FIGURE_ITEMS." fi ON fm.figure_manufacturer_id = fi.figure_manufacturer
											WHERE figure_id='".$data['figure_id']."' 
											");						
									if(dbrows($manufacturer)){
									while($datamanufacturer = dbarray($manufacturer)){
				
				echo "<td>".trimlink($datamanufacturer['figure_manufacturer_name'],10)."</td>\n";
									}}		
									$scale = dbquery("
										SELECT 
												fs.figure_scale_id,
												fs.figure_scale_name,
												fi.figure_scale
											FROM ".DB_FIGURE_SCALES." fs
											INNER JOIN ".DB_FIGURE_ITEMS." fi ON fs.figure_scale_id = fi.figure_scale
											WHERE figure_id='".$data['figure_id']."' 
											");				
									if(dbrows($scale)){
									while($datascale = dbarray($scale)){
										
				echo "<td>".$datascale['figure_scale_name']."</td>\n";
									}	}	
			
				echo "<td>".profile_link($data['user_id'], $data['user_name'], $data['user_status'])."</td>\n";
				echo "<td>".timer($data['figure_datestamp'])."</td>\n";
				//echo "<td>".$data['figure_id']."</td>\n";
			echo "</tr>\n";
		
	}	
			echo "</tbody>\n</table>\n";
	
	} else {
		
		// $locale['figs_0022'] = "There are currently no figure pendings";
		echo "<p><div class='alert alert-warning' role='alert'><div class='text-center'>".$locale['figs_0022']."</div></div>\n";
	}
