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
require_once file_exists('maincore.php') ? 'maincore.php' : __DIR__."/../../maincore.php";
include INFUSIONS."figurelib/infusion_db.php";
require_once THEMES."templates/header.php";
require_once INCLUDES."infusions_include.php";
require_once INFUSIONS."figurelib/_functions.inc.php";

// GET GLOBAL VARIABLES
global $aidlink;
global $settings;
global $userdata;

// LANGUAGE
if (file_exists(INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php")) {
    include INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php";
} else {
    include INFUSIONS."figurelib/locale/English/locale_figurelib.php";
}

if (!db_exists(DB_FIGURE_ITEMS)) { redirect(BASEDIR."error.php?code=404"); }

opentable("<strong>".$locale['mc_0001']."</strong>");	

	if (iMEMBER) {
		
			$count = dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_user_id='".$userdata['user_id']."'");
			$total_rows = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe=1 AND ".groupaccess("figure_visibility")."");														
		if ($count != 0) {	
				
			//openside("Mini Stats");
			  openside("");				

				echo "<div class='row'>\n";	
									
								// YOUR FIGURE COUNTER + YOUR LAST FIGURE 	
								// echo "<div class='col-lg-3 col-md-3 col-sm-12 col-xs-12'>\n";
								// echo "<div class='col-lg-3 col-md-3 col-sm-12 col-xs-12'>\n";
								// echo "<div class='col-lg-3 col-md-3 col-sm-12 col-xs-12'>\n";
								// echo "<div class='col-lg-3 col-md-3 col-sm-12 col-xs-12'>\n";	
								
								// MY FIGURES COUNTER
									echo "<div class='col-lg-3 col-md-3 col-sm-12 col-xs-12'>\n";
									echo "<div class='navbar-default text-bold'>";										
									// ['mc_0007']= "Figure Counter: ";
									echo $locale['mc_0007'];			
									echo "</div>";
									echo "</div>";
						
									echo "<div class='col-lg-3 col-md-3 col-sm-12 col-xs-12'>\n";				
										echo "<button class='btn btn-primary btn-xs' type='button'><span class='badge'>".$count." / ".$total_rows."</button>\n";
									echo "</div>";

								// YOUR LAST FIGURE  										
									global $userdata;
									$resultlast = dbquery(
										"SELECT f.figure_id,
												f.figure_title, 			
												f.figure_submitter, 
												f.figure_freigabe, 
												f.figure_pubdate, 
												f.figure_scale, 
												f.figure_title, 
												f.figure_manufacturer, 
												f.figure_brand, 
												f.figure_datestamp, 
												f.figure_cat, 
												fc.figure_cat_id, 
												fc.figure_cat_name, 
												fm.figure_manufacturer_name, 
												fb.figure_brand_name, 
												fs.figure_scale_id, 
												fs.figure_scale_name, 						
												fuf.figure_userfigures_figure_id, 	
												fuf.figure_userfigures_user_id 		
										FROM ".DB_FIGURE_ITEMS." f
										LEFT JOIN ".DB_FIGURE_USERFIGURES." fuf ON fuf.figure_userfigures_figure_id=f.figure_id
										LEFT JOIN ".DB_FIGURE_CATS." fc ON f.figure_cat=fc.figure_cat_id
										LEFT JOIN ".DB_FIGURE_MANUFACTURERS." fm ON fm.figure_manufacturer_id = f.figure_manufacturer
										LEFT JOIN ".DB_FIGURE_BRANDS." fb ON fb.figure_brand_id = f.figure_brand
										LEFT JOIN ".DB_FIGURE_SCALES." fs ON fs.figure_scale_id = f.figure_scale
										".(multilang_table("FI") ? "WHERE figure_language='".LANGUAGE."' AND" : "WHERE")." figure_userfigures_user_id='".$userdata['user_id']."'
										ORDER BY figure_datestamp DESC LIMIT 0,1 
									");
										if (dbrows($resultlast) != 0) {
											while($data = dbarray($resultlast)){
										
												echo "<div class='col-lg-3 col-md-3 col-sm-12 col-xs-12'>\n";
													echo "<div class='navbar-default text-bold'>";													
														//['mc_0009']= "Last figure: ";
														echo "<div class='side-small'>".$locale['mc_0009']."</div>";
													echo "</div>";
												echo "</div>";												
												
												echo "<div class='col-lg-3 col-md-3 col-sm-12 col-xs-12'>\n";						
													// TEXT ANZEIGEN
													echo"<a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."' alt='".$data['figure_title']." [".$data['figure_manufacturer_name']."]' title='".trimlink($data['figure_title'],20)." [".$data['figure_manufacturer_name']."]'>".trimlink($data['figure_title'], 20)."</a>";
													
													// BILD ANZEIGEN
													//echo "<div class='side-small figurelib-inforow-height'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>\n<img src='".figures_getImagePath("figure", "thumb2", $data['figure_id'])."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' /></a></div>";
													
												echo "</div>";
								
											}
									
										}
							
				echo "</div>";
			
			closeside();
						
				/////////////////////////////////////////////////////////////////////////////////////////////////		
				// PANEL OF ALL FIGURE FROM USER  ///////////////////////////////////////////////////////////////
				/////////////////////////////////////////////////////////////////////////////////////////////////
						
			//openside($locale['yours']);
			openside("");
						
				global $userdata;
				$figurelibSettings = get_settings("figurelib"); 
				$info = array();
				$info['item'] = array();
				
				$resultnav = dbquery("
					  SELECT
						f.figure_id,
						f.figure_freigabe,
						fuf.figure_userfigures_user_id
					  FROM ".DB_FIGURE_ITEMS." f
					  LEFT JOIN ".DB_FIGURE_USERFIGURES." fuf ON fuf.figure_userfigures_user_id=f.figure_id
					  WHERE figure_freigabe='1' AND ".groupaccess("figure_visibility")."");	
			
			if (dbrows($resultnav) != 0) {
				
					$cdata = dbarray($resultnav);
					$info = $cdata;
				
					$max_rows = dbcount("(figure_userfigures_figure_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_user_id='".$userdata['user_id']."'");
					$_GET['rowstart'] = isset($_GET['rowstart']) && isnum($_GET['rowstart']) && $_GET['rowstart'] <= $max_rows ? $_GET['rowstart'] : 0;
									
								$result = dbquery("SELECT 
									tb.figure_id, 
									tb.figure_submitter, 
									tb.figure_freigabe, 
									tb.figure_pubdate, 
									tb.figure_scale, 
									tb.figure_title, 
									tb.figure_manufacturer, 
									tb.figure_brand, 
									tb.figure_datestamp, 
									tb.figure_cat, 
									tbc.figure_cat_id, 
									tbc.figure_cat_name, 
									tbu.user_id, 
									tbu.user_name, 
									tbu.user_status, 
									tbu.user_avatar, 
									tbm.figure_manufacturer_name, 
									tbb.figure_brand_name, 
									tbs.figure_scale_id, 
									tbs.figure_scale_name, 							
									fuf.figure_userfigures_figure_id, 
									fuf.figure_userfigures_user_id 
								FROM ".DB_FIGURE_ITEMS." tb
								LEFT JOIN ".DB_USERS." tbu ON tb.figure_submitter=tbu.user_id
								INNER JOIN ".DB_FIGURE_USERFIGURES." fuf ON fuf.figure_userfigures_figure_id=tb.figure_id
								INNER JOIN ".DB_FIGURE_CATS." tbc ON tb.figure_cat=tbc.figure_cat_id
								INNER JOIN ".DB_FIGURE_MANUFACTURERS." tbm ON tbm.figure_manufacturer_id = tb.figure_manufacturer
								INNER JOIN ".DB_FIGURE_BRANDS." tbb ON tbb.figure_brand_id = tb.figure_brand
								INNER JOIN ".DB_FIGURE_SCALES." tbs ON tbs.figure_scale_id = tb.figure_scale
								".(multilang_table("FI") ? "WHERE figure_language='".LANGUAGE."' AND" : "WHERE")." tb.figure_freigabe='1' AND ".groupaccess("tb.figure_visibility")."
								AND figure_userfigures_user_id='".$userdata['user_id']."'
								LIMIT ".$_GET['rowstart'].",".$figurelibSettings['figure_per_page']."
								");
					
					$numrows = dbrows($result);
					$info['figure_rows'] = $numrows;
					$figurelibSettings = get_settings("figurelib");
					
					$info['page_nav'] = $max_rows > $figurelibSettings['figure_per_page'] ? makepagenav($_GET['rowstart'], $figurelibSettings['figure_per_page'], $max_rows, 3, INFUSIONS."figurelib/mycollection.php?") : 0;
					
				// WENN DATEN UNGLEICH = 0 DANN DARSTELLUNG DER DATEN
				if (dbrows($result) > 0) {				
				
							// TITLE LINE
							echo "<div class='row'>\n";	
							echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default text-bold'>";					
								echo "<span><strong>".$locale['yours']."</strong></span>\n";
							echo "</div>\n";
							echo "</div>\n";
							echo "</div>\n";							

						echo "<hr>";	
															
				echo "<div class='text-bold'>";				
				echo "<div class='container-fluid'>\n";
				//echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	

								/*//// GRID SYSTEM FOR PANEL AS OVERVIEW ///////////////////////////////////////
								echo "<div class='col-lg-1 col-md-1 col-sm-2 col-xs-2'>\n";		image
								echo "<div class='col-lg-2 col-md-2 col-sm-4 col-xs-4'>\n";		title
								echo "<div class='col-lg-2 col-md-2 col-sm-4 col-xs-4'>\n";		manufacturer
								echo "<div class='col-lg-2 hidden-2 hidden-sm hidden-xs'>\n";	brand
								echo "<div class='col-lg-1 hidden-1 hidden-sm hidden-xs'>\n";	scale
								echo "<div class='col-lg-1 col-md-1 hidden-sm hidden-xs'>\n";	year
								echo "<div class='col-lg-2 hidden-2 hidden-sm hidden-xs'>\n";	rating
								echo "<div class='col-lg-1 col-md-1 col-sm-2 col-xs-2'>\n";		add/remove
								//////////////////////////////////////////////////////////////////////////////*/
				
								// COLUMN 1 (image)
								echo "<div class='col-lg-1 col-md-1 col-sm-2 col-xs-2'>\n";	
									echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_018']."</div>\n";
								echo "</div>\n";

								// COLUMN 2 (name of figure)
								echo "<div class='col-lg-2 col-md-2 col-sm-4 col-xs-4'>\n";
									echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_002']."</div>\n";
								echo "</div>\n";						
								
								// COLUMN 3 (manufacturer)
								echo "<div class='col-lg-2 col-md-2 col-sm-4 col-xs-4'>\n";
									echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_003']."</div>\n";
								echo "</div>\n";
								
								// COLUMN 4 (brand)
								echo "<div class='col-lg-2 hidden-2 hidden-sm hidden-xs'>\n";
									echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_004']."</div>\n";
								echo "</div>\n";
								
								// COLUMN 5 (scale)
								echo "<div class='col-lg-1 hidden-1 hidden-sm hidden-xs'>\n";
									echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_005']."</div>\n";
								echo "</div>\n";
								
								// COLUMN 6 (release date)
								echo "<div class='col-lg-1 col-md-1 hidden-sm hidden-xs'>\n";
									echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_006']."</div>\n";
								echo "</div>\n";
																
								// COLUMN 7 (rating)
								echo "<div class='col-lg-2 hidden-2 hidden-sm hidden-xs'>\n";
									echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_010']."</div>\n";
								echo "</div>\n";	
								
								// COLUMN 8 (edit)
								echo "<div class='col-lg-1 col-md-1 col-sm-2 col-xs-2'>\n";	
									echo "<div class='text-smaller text-uppercase'>Opt.</div>\n";
								echo "</div>\n";												
						
						echo "</div>\n";
						//echo "</div>\n";
						echo "</div>\n";
						echo "</div>\n";
						
						echo "<hr>";
				 
				 while($data = dbarray($result)){
					 
						echo "<div class='container-fluid'>\n";
						//echo "<div class='table-responsive'>\n";
						echo "<div class='row'>\n";		
				 
							
								
								// Get Image
									$imageURL = figures_getImagePath("figure", "thumb2", $data['figure_id']);
					
								// COLUMN 1 (image clickable)
									echo "<div class='col-lg-1 col-md-1 col-sm-2 col-xs-2'>\n";	
										echo "<div class='side-small figurelib-inforow-height'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>\n<img src='".figures_getImagePath("figure", "thumb2", $data['figure_id'])."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' /></a>";
									echo "</div></div>\n";					
									
								// COLUMN 2 (name of figure)
									echo "<div class='col-lg-2 col-md-2 col-sm-4 col-xs-4'>\n";
											echo "<div class='side-small figurelib-inforow-height'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."'>".trimlink($data['figure_title'],8)."</a>";
											echo "</div></div>\n";	
																				
								// COLUMN 3 (manufacturer)
									echo "<div class='col-lg-2 col-md-2 col-sm-4 col-xs-4'>\n";
											echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_003']." : ".$data['figure_manufacturer_name']."' alt='".$locale['CLFP_003']." : ".$data['figure_manufacturer_name']."'>".trimlink($data['figure_manufacturer_name'],10)."</div>\n";
											echo "</div>\n";
								
								// COLUMN 4 (brand)
									echo "<div class='col-lg-2 hidden-2 hidden-sm hidden-xs'>\n";
											echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_004']." : ".$data['figure_brand_name']."' alt='".$locale['CLFP_004']." : ".$data['figure_brand_name']."'>".trimlink($data['figure_brand_name'],10)."</div>\n";
											echo "</div>\n";
								
								// COLUMN 5 (scale)
									echo "<div class='col-lg-1 hidden-1 hidden-sm hidden-xs'>\n";
											echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_005']." : ".$data['figure_scale_name']."' alt='".$locale['CLFP_005']." : ".$data['figure_scale_name']."'>".trimlink($data['figure_scale_name'],6)."</div>\n";
											echo "</div>\n";
					
									// No release date or unknown = "no data" 
									if ($data['figure_pubdate'] == "") {
										
												// COLUMN 6 (release date)
												echo "<div class='col-lg-1 col-md-1 hidden-sm hidden-xs'>\n";
													echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_006']." : ".$locale['CLFP_008']."' alt='".$locale['CLFP_006']." : ".$locale['CLFP_008']."'>".trimlink($locale['CLFP_008'],7)."</div>\n";
												echo "</div>\n";			
									} else {
										
												echo "<div class='col-lg-1 col-md-1 hidden-sm hidden-xs'>\n";
													echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_006']." : ".$data['figure_pubdate']."' alt='".$locale['CLFP_006']." : ".$data['figure_pubdate']."'>".trimlink($data['figure_pubdate'],7)."</div>\n";
												echo "</div>\n";						
									} 

								// COLUMN 7 (rating)
								$drating = dbarray(dbquery("
								   SELECT 
										SUM(rating_vote) sum_rating, 
										COUNT(rating_item_id) count_votes 
										FROM ".DB_RATINGS." 
										WHERE rating_type='FI' 
										AND  rating_item_id='".$data['figure_id']."'
									")); 
		   
								$rating = ($drating['count_votes'] > 0 ? str_repeat("<img src='".INFUSIONS.$inf_folder."/images/starsmall.png'>",ceil($drating['sum_rating']/$drating['count_votes'])) : "-");
								
								echo "<div class='col-lg-2 hidden-2 hidden-sm hidden-xs'>\n";
									echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_010']."' alt='".$locale['CLFP_010']."'>".$rating."</div>\n";
								echo "</div>\n";
																
								// COLUMN 8 (edit)
								echo "<div class='col-lg-1 col-md-1 col-sm-2 col-xs-2'>\n";	
									echo "<div class='side-small figurelib-inforow-height'>\n";
										echo figures_displayMyCollectionForm($data['figure_id']);
								echo "</div>\n";
								echo "</div>\n";
							echo "</div>\n";
						echo "</div>\n";
						//echo "</div>\n";
							
							}	
	
												
						
						// PAGE NAV
						echo $info['page_nav'] ? "<div class='text-right'>".$info['page_nav']."</div>" : '';
						
						echo "<hr>\n";
																			
				}
			
			} 

				closeside();
								
	
		} else {	
									
								//['mc_0010']= "You have no figures";
								echo "<div class='alert alert-info' role='alert'>";
									echo $locale['mc_0010'];
								echo "</div>\n";
		}		
		
	} else {
									
			openside("");
				echo "<div class='alert alert-danger' role='alert'>";
					//$locale['mc_0011']= "This feature is only available for registered members. Please Sign up ";
					echo $locale['mc_0011'];
					//$locale['mc_0012']= "HERE";
					echo "<a href='".BASEDIR."register.php'>".$locale['mc_0012']."</a>";
				echo "</div>\n";
			closeside();
			
			}
			
closetable();

/////////////////////////////////////////////////////////////////////////////////////////////	
	
require_once THEMES."templates/footer.php";
