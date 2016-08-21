<?php

/***************************************************************************
 *   figure_stats_panel.php for FIGURELIB                                        *
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
require_once "../../../maincore.php";
require_once THEMES."templates/header.php";
global $aidlink;
include INFUSIONS."figurelib/infusion_db.php";
require_once INCLUDES."infusions_include.php";

		// LANGUAGE
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
opentable("<strong>Figure Stats</strong>");


// GESAMTZAHL ALLER FIGUREN //////////////////////////////////
				$allFiguresCounter = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe='1'");
				
		echo "<div class='table-responsive'>\n";
		echo "<div class='row'>\n";	
				
					echo "<div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>\n";
					echo "<div class='navbar-default'>";	
					echo "<span>Gesamtzahl aller Figuren: </span>\n";
					echo "</div></div>\n";

					echo "<div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>\n";
					echo "<span class='badge'>".$allFiguresCounter."</span>\n";
					echo "</div>\n";
	
		echo "</div>\n";
		echo "</div>\n";

		// Gesamtzahl Figuren in Userbesitz //////////////////////////////////
	
				$allFiguresCounterUser = dbcount("(figure_userfigures_id)",  DB_FIGURE_USERFIGURES, "");	

		echo "<div class='table-responsive'>\n";
		echo "<div class='row'>\n";	
				
					echo "<div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>\n";
					echo "<div class='navbar-default'>";	
					echo "<span>Gesamtzahl Figuren in Userbesitz:</span>\n";
					echo "</div></div>\n";

					echo "<div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>\n";
					echo "<span class='badge'>".$allFiguresCounterUser."</span>\n";
					echo "</div>\n";
	
		echo "</div>\n";
		echo "</div>\n";

// TEUERSTE FIGUR //////////////////////////////////

						$resultteuerste = dbquery("
							SELECT 
								tb.figure_id, tb.figure_submitter, tb.figure_retailprice, tb.figure_usedprice, tb.figure_freigabe, tb.figure_pubdate, tb.figure_scale, tb.figure_title, tb.figure_manufacturer, tb.figure_brand, tb.figure_datestamp, tb.figure_cat, 
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
							WHERE ".(multilang_table("FI") ? "tb.figure_language='".LANGUAGE."' AND" : "")." tb.figure_freigabe='1' 
							ORDER BY tb.figure_retailprice DESC LIMIT 0,1 

						");
				
					if (dbrows($resultteuerste) != 0) {
						while ($data = dbarray($resultteuerste)) {
																		
					echo "<div class='table-responsive'>\n";
					echo "<div class='row'>\n";	
				
						echo "<div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>\n";
						echo "<div class='navbar-default'>";	
						echo "<span>H&ouml;chster Neupreis:</span>\n";
						echo "</div></div>\n";

						echo "<div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>\n";
						echo "<span class=''>".$data['figure_retailprice']." (".$data['figure_title']." / ".$data['figure_manufacturer_name'].")</span>\n";
						echo "</div>\n";
	
				echo "</div>\n";
				echo "</div>\n";
												
					}
						}
						

// HÖCHSTER GEBRAUCHTPREIS //////////////////////////////////

		$resultteuerstegebraucht = dbquery("
					SELECT 
						tb.figure_id, tb.figure_submitter, tb.figure_retailprice, tb.figure_usedprice, tb.figure_freigabe, tb.figure_pubdate, tb.figure_scale, tb.figure_title, tb.figure_manufacturer, tb.figure_brand, tb.figure_datestamp, tb.figure_cat, 
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
					WHERE ".(multilang_table("FI") ? "tb.figure_language='".LANGUAGE."' AND" : "")." tb.figure_freigabe='1' 
					ORDER BY tb.figure_usedprice DESC LIMIT 0,1 

				");
				
					if (dbrows($resultteuerstegebraucht) != 0) {
						while ($data = dbarray($resultteuerstegebraucht)) {	

					echo "<div class='table-responsive'>\n";
					echo "<div class='row'>\n";	
				
						echo "<div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>\n";
						echo "<div class='navbar-default'>";	
						echo "<span>H&ouml;chster Gebrauchtpreis:</span>\n";
						echo "</div></div>\n";

						echo "<div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>\n";
						echo "<span class=''>".$data['figure_usedprice']." (".$data['figure_title']." / ".$data['figure_manufacturer_name'].")</span>\n";
					
					echo "</div>\n";
					echo "</div>\n";
					echo "</div>\n";						
					}
						}
//////////////////////////////////



// Close Site
closetable();

// Important File
require_once THEMES."templates/footer.php";


