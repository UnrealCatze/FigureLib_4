<?php

/***************************************************************************
 *   mycollection.php for FIGURELIB                                        *
 *                                                                         *
 *   Copyright (C) 2016 Catzenjaeger                                       *
 *   www.AlienCollectors.com                                               *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 ***************************************************************************/

// Important Files
require_once file_exists('maincore.php') ? 'maincore.php' : __DIR__."/../../maincore.php";
include INFUSIONS."figurelib/infusion_db.php";
require_once THEMES."templates/header.php";
require_once INCLUDES."infusions_include.php";

// Locale
if (file_exists(INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php")) {
    include INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php";
} else {
    include INFUSIONS."figurelib/locale/English/locale_figurelib.php";
}

// If Infusion isn't installed, redirect to Errorpage
if (!db_exists(DB_FIGURE_ITEMS)) { redirect(BASEDIR."error.php?code=404"); }

// Get Settings
$fil_settings = get_settings("figurelib");
		
// Start Site
opentable("<strong>".$locale['mc_0001']."</strong>");

// Display Content, if the User is a Member
if (iMEMBER) {
		
	// Hande MyCollection Actions
	
	// Add a Figure to Collection
	if (
		isset($_POST['add_figure']) && isNum($_POST['add_figure']) && 
		dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_id='".$_POST['add_figure']."' AND figure_freigabe='1'") &&
		!dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$_POST['add_figure']."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'")
	) {
		dbquery("
			INSERT INTO ".DB_FIGURE_USERFIGURES."
				(figure_userfigures_figure_id, figure_userfigures_user_id, figure_userfigures_language)
			VALUES
				('".$_POST['add_figure']."', '".$userdata['user_id']."', '".LANGUAGE."')
		");
		addNotice("success", "Figure is successfully added to your Collection.");
	
	// Delete a Figure from Collection
	} elseif (
		isset($_POST['remove_figure']) && isNum($_POST['remove_figure']) && 
		dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_id='".$_POST['remove_figure']."' AND figure_freigabe='1'") &&
		dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$_POST['remove_figure']."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'")
	) {
		dbquery("
			DELETE FROM ".DB_FIGURE_USERFIGURES."
			WHERE figure_userfigures_figure_id='".$_POST['remove_figure']."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'
		");
		addNotice("danger", "Figure is successfully removed from your Collection.");
	}

	

	// Display Figures Counter and last Figure START
	echo "<div class='row'>\n";

		// Display my Figures Counter START
		echo "<div class='col-lg-4 col-md-12 col-sm-12 col-xs-12'>\n";
			openside($locale['mc_0006']);
			
			// Counters
			$myFiguresCounter  = dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_user_id='".$userdata['user_id']."'");
			$allFiguresCounter = dbcount("(figure_id)",             DB_FIGURE_ITEMS,       "figure_freigabe='1'");
			
			// Display Counter
			if ($myFiguresCounter) {
				echo $locale['mc_0007'];
				echo "<span class='badge'>".$myFiguresCounter." / ".$allFiguresCounter."</span>\n";
				
			// Display Message
			} else {
				echo $locale['mc_0010'];
			}
			
		// Display my Figures Counter END
			closeside();
		echo "</div>\n";

		
		
		// Display last Figure START
		echo "<div class='col-lg-8 col-md-12 col-sm-12 col-xs-12'>\n";
			openside($locale['mc_0005']);

			// Get Data for last Figure 
			$lastResult = dbquery("
				SELECT
					f.figure_id, f.figure_title, fm.figure_manufacturer_name
				FROM ".DB_FIGURE_ITEMS." AS f 
				LEFT JOIN ".DB_FIGURE_MANUFACTURERS." AS fm ON fm.figure_manufacturer_id=f.figure_manufacturer
				LEFT JOIN ".DB_FIGURE_USERFIGURES." AS fu ON fu.figure_userfigures_figure_id=f.figure_id
				WHERE ".(multilang_table("FI") ? "f.figure_language='".LANGUAGE."' AND" : "")." fu.figure_userfigures_user_id='".$userdata['user_id']."'
				ORDER BY f.figure_datestamp DESC
				LIMIT 0,1 
			");
			
			// Display Figure, if there is One
			if (dbrows($lastResult)) {
				$lastData = dbarray($lastResult);
				
				echo "<span class='side-small'>".$locale['mc_0009']."</span>\n";
				echo "<a href='".INFUSIONS."figurelib/figures.php?figure_id=".$lastData['figure_id']."' title='".$lastData['figure_title']."'>\n";
					echo trimlink($lastData['figure_title'], 25)." [".trimlink($lastData['figure_manufacturer_name'], 20)."]\n";
				echo "</a>\n";
				
			// Or display Message
			} else {
				echo $locale['mc_0010'];
			}
			
		// Display my Figures Counter END
			closeside();
		echo "</div>\n";
			
	// Display Figures Counter and last Figure END
	echo "</div>\n";


	// Display my Collection START
	echo "<hr />\n";
	
	echo "<div class='row'>\n";
		echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
			openside($locale['yours']);
			
			// Count all my Figures
			$myFiguresCount = dbrows(dbquery("
				SELECT
					fu.figure_userfigures_id
				FROM ".DB_FIGURE_USERFIGURES." AS fu
				LEFT JOIN ".DB_FIGURE_ITEMS." AS f ON f.figure_id=fu.figure_userfigures_figure_id
				WHERE f.figure_freigabe='1' AND fu.figure_userfigures_user_id='".$userdata['user_id']."'
			"));

			// Check Site
			$_GET['rowstart'] = isset($_GET['rowstart']) && isnum($_GET['rowstart']) && $_GET['rowstart'] <= $myFiguresCount ? $_GET['rowstart'] : 0;
			
			// If there exists some Figures in my Collection, display it
			if ($myFiguresCount) {
				
				// Select Figures for Page
				$result = dbquery("
					SELECT 
						tb.figure_id, tb.figure_submitter, tb.figure_freigabe, tb.figure_pubdate, tb.figure_scale, tb.figure_title, tb.figure_manufacturer, tb.figure_brand, tb.figure_datestamp, tb.figure_cat, 
						tbc.figure_cat_id, tbc.figure_cat_name, 
						tbu.user_id, tbu.user_name, tbu.user_status, tbu.user_avatar, 
						tbm.figure_manufacturer_name, 
						tbb.figure_brand_name, 
						tby.figure_year_id, tby.figure_year, 
						tbs.figure_scale_id, tbs.figure_scale_name, 							
						fuf.figure_userfigures_figure_id, fuf.figure_userfigures_user_id 
					FROM ".DB_FIGURE_ITEMS." AS tb
					LEFT JOIN ".DB_USERS." AS tbu ON tb.figure_submitter=tbu.user_id
					LEFT JOIN ".DB_FIGURE_USERFIGURES." AS fuf ON fuf.figure_userfigures_figure_id=tb.figure_id
					LEFT JOIN ".DB_FIGURE_CATS." AS tbc ON tb.figure_cat=tbc.figure_cat_id
					LEFT JOIN ".DB_FIGURE_MANUFACTURERS." AS tbm ON tbm.figure_manufacturer_id = tb.figure_manufacturer
					LEFT JOIN ".DB_FIGURE_BRANDS." AS tbb ON tbb.figure_brand_id = tb.figure_brand
					LEFT JOIN ".DB_FIGURE_SCALES." AS tbs ON tbs.figure_scale_id = tb.figure_scale
					LEFT JOIN ".DB_FIGURE_YEARS." AS tby ON tby.figure_year_id = tb.figure_pubdate
					WHERE ".(multilang_table("FI") ? "tb.figure_language='".LANGUAGE."' AND" : "")." tb.figure_freigabe='1' AND fuf.figure_userfigures_user_id='".$userdata['user_id']."'
					LIMIT ".$_GET['rowstart'].",".$fil_settings['figure_per_page']."
				");
				
			/*
				echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";  	// Image
				echo "<div class='col-lg-2 col-md-2 col-sm-4 col-xs-4'>\n"; 	// Name of Figure
				echo "<div class='col-lg-3 col-md-2 col-sm-4 col-xs-4'>\n"; 	// Manufacturer
				echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n"; 	// Brand
				echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n"; 	// Scale
				echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n"; 	// Release Date
				echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n"; 	// Rating
				echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n"; 	// Add/Remove
			*/
								
				// Display Header for my Collection - START
				echo "<div class='navbar-default'>";
				echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";
				
					// Image
					echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";
						echo "<span class='text-smaller text-uppercase'>".$locale['CLFP_018']."</span>\n";
					echo "</div>\n";
					
					// Name of Figure
					echo "<div class='col-lg-2 col-md-2 col-sm-4 col-xs-4'>\n";
						echo "<span class='text-smaller text-uppercase'>".$locale['CLFP_002']."</span>\n";
					echo "</div>\n";
					
					// Manufacturer
					echo "<div class='col-lg-3 col-md-2 col-sm-4 col-xs-4'>\n";
						echo "<span class='text-smaller text-uppercase'>".$locale['CLFP_003']."</span>\n";
					echo "</div>\n";
					
					// Brand
					echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";
						echo "<span class='text-smaller text-uppercase'>".$locale['CLFP_004']."</span>\n";
					echo "</div>\n";
					
					// Scale
					echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";
						echo "<span class='text-smaller text-uppercase'>".$locale['CLFP_005']."</span>\n";
					echo "</div>\n";
					
					// Release Date
					echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
						echo "<span class='text-smaller text-uppercase'>".$locale['CLFP_006']."</span>\n";
					echo "</div>\n";
					
					// Rating
					echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
						echo "<span class='text-smaller text-uppercase'>".$locale['CLFP_010']."</span>\n";
					echo "</div>\n";
					
					// Add/Remove
					echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";
						echo "<span class='text-smaller text-uppercase'>Opt.</span>\n";
					echo "</div>\n";
					
				// Display Header for my Collection - END
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				echo "<hr>";
				// Display the Items
				while ($data = dbarray($result)) {
					
					$drating = dbarray(dbquery("
								   SELECT 
										SUM(rating_vote) sum_rating, 
										COUNT(rating_item_id) count_votes 
										FROM ".DB_RATINGS." 
										WHERE rating_type='FI' 
										AND  rating_item_id='".$data['figure_id']."'
									")); 
   
								$rating = ($drating['count_votes'] > 0 ? str_repeat("<img src='".INFUSIONS.$inf_folder."/images/starsmall.png'>", ceil($drating['sum_rating']/$drating['count_votes'])) : "-");
												
					// Get correct Image
					if (dbcount("(figure_images_image_id)", DB_FIGURE_IMAGES, "figure_images_figure_id='".$data['figure_id']."'")) {
						$imageData = dbarray(dbquery("
							SELECT
								figure_images_image, figure_images_thumb
							FROM ".DB_FIGURE_IMAGES."
							WHERE figure_images_figure_id='".$data['figure_id']."'
							LIMIT 0,1
						"));
						if ($imageData['figure_images_thumb'] && @file_exists(THUMBS_FIGURES.$imageData['figure_images_thumb'])) {
							$imageURL = THUMBS_FIGURES.$imageData['figure_images_thumb'];
						} elseif ($imageData['figure_images_image'] && @file_exists(IMAGES_FIGURES.$imageData['figure_images_image'])) {
							$imageURL = IMAGES_FIGURES.$imageData['figure_images_image'];
						} else {
							$imageURL = INFUSIONS."figurelib/images/default.png";
						}
					} else {
						$imageURL = INFUSIONS."figurelib/images/default.png";
					}
					
				// Display Header for my Collection - START					
				echo "<div class='row'>\n";
				
				
						// Image
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";
							echo "<a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."' title='".$locale['CLFP_002'].": ".$data['figure_title']."' class='side-small'>\n";
								echo "<img src='".$imageURL."' alt='".$locale['CLFP_002'].": ".$data['figure_title']."' title='".$locale['CLFP_002'].": ".$data['figure_title']."' style='border: 0px; max-width: 40px; max-height: 40px;' />\n";
							echo "</a>\n";
						echo "</div>\n";
						
						// Name of Figure
						echo "<div class='col-lg-2 col-md-2 col-sm-4 col-xs-4'>\n";
							echo "<a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."' title='".$locale['CLFP_002'].": ".$data['figure_title']."' class='side-small'>\n";
								echo trimlink($data['figure_title'], 10);
							echo "</a>\n";
						echo "</div>\n";
						
						// Manufacturer
						echo "<div class='col-lg-3 col-md-2 col-sm-4 col-xs-4'>\n";
							echo "<span title='".$locale['CLFP_003'].": ".$data['figure_manufacturer_name']."' class='side-small'>\n";
								echo trimlink($data['figure_manufacturer_name'], 10);
							echo "</span>\n";
						echo "</div>\n";			
						
						// Brand
						echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";
							echo "<span title='".$locale['CLFP_004'].": ".$data['figure_brand_name']."' class='side-small'>\n";
								echo trimlink($data['figure_brand_name'], 10);
							echo "</span>\n";
						echo "</div>\n";
						
						// Scale
						echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";
							echo "<span title='".$locale['CLFP_005'].": ".$data['figure_scale_name']."' class='side-small'>\n";
								echo trimlink($data['figure_scale_name'], 6);
							echo "</span>\n";
						echo "</div>\n";
						
						// Release Date
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							if (!$data['figure_pubdate']) {
								echo "<span title='".$locale['CLFP_006'].": ".$locale['CLFP_008']."' class='side-small'>\n";
									echo trimlink($locale['CLFP_008'], 6);
								echo "</span>\n";
							} else {
								echo "<span title='".$locale['CLFP_006'].": ".$data['figure_year']."' class='side-small'>\n";
									echo trimlink($data['figure_year'], 6);
								echo "</span>\n";
							}
						echo "</div>\n";
						
						// Rating
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<span title='".$locale['CLFP_010']."' class='side-small'>\n";
								echo $rating;
							echo "</span>\n";
						echo "</div>\n";
						
						// add/remove
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n"; 
					// Check if User has it in his Collection
								echo openform("collectionform", "post", FUSION_SELF);
								if (dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'")) {
									echo form_button("remove_figure", "", $data['figure_id'], ["class" => "btn-xs btn-success", "alt" => $locale['userfigure_002'], "icon" => "fa fa-fw fa-check-square-o"]);
								} else {
									echo form_button("add_figure", "", $data['figure_id'], ["class" => "btn-xs btn-default", "alt" => $locale['userfigure_001'], "icon" => "fa fa-fw fa-plus-square-o"]);
								}
								echo closeform();
						echo "</div>\n";								
								
					// Display Table for my Collection - END
					echo "</div>\n";

				}
				
				// Display Page Nav if needed
				if ($myFiguresCount > $fil_settings['figure_per_page']) {
					echo "<div class='text-right'>\n";
						echo makepagenav($_GET['rowstart'], $fil_settings['figure_per_page'], $myFiguresCount, 3, INFUSIONS."figurelib/mycollection.php?");
					echo "</div>\n";
				}
				
			// Display Message, if User has no Figures in Collection
			} else {
				echo "<div class='text-center'>".$locale['CLFP_001']."</div>\n";
			}


			// Display Admin Options
			if (iADMIN || iSUPERADMIN) {
				echo "<hr />\n";
				echo "<div class='text-center'>\n";
					echo "<a href='".INFUSIONS."figurelib/admin.php".$aidlink."' title='".$locale['CLFP_016']."'>".$locale['CLFP_016']."</a>\n";
				echo "</div>\n";
				echo "<hr />\n";
			}

	// Display my Collection START
			closeside();
		echo "</div>";
	echo "</div>";		

// Display Message if the User is a Guest
} else {
	$locale['mc_0001'] = "My Figure Collection";
	$locale['mc_0011'] = "This feature is only available for registered members. Please Sign up ";
	$locale['mc_0012'] = "HERE";
	openside($locale['mc_0001']);
		echo $locale['mc_0011'];
		echo "<a href='".BASEDIR."register.php' title='".$locale['mc_0012']."'>".$locale['mc_0012']."</a>\n";
	closeside();
}

// Close Site
closetable();

// Important File
require_once THEMES."templates/footer.php";

?>
