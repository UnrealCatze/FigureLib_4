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

// LANGUAGE
if (file_exists(INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php")) {
    include INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php";
} else {
    include INFUSIONS."figurelib/locale/English/locale_figurelib.php";
}

if (!db_exists(DB_FIGURE_ITEMS)) { redirect(BASEDIR."error.php?code=404"); }

	if (iMEMBER) {

				// GET GLOBAL VARIABLES
				global $aidlink;
				global $settings;
				global $userdata;
	
		opentable("<strong>".$locale['mc_0001']."</strong>");

		echo "<div class='col-lg-4 col-md-12 col-sm-12 col-xs-12'>\n";		

/////////////////////////////////////////////////////////////////////////////////////////////////		
// My figures counter ///////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

		openside($locale['mc_0006']);
				
			$count = dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_user_id='".$userdata['user_id']."'");
			$total_rows = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe=1 AND ".groupaccess("figure_visibility")."");				
			
			if ($count != 0) {
						
					echo $locale['mc_0007'];
					echo "<span class='badge'>";
					echo $count." / ".$total_rows;
					echo "</span>";
					//echo $locale['mc_0008'];
					
			} else {	
					
					echo $locale['mc_0010'];
			}
		closeside();
		
		echo "</div>";
		echo "<div class='col-lg-8 col-md-12 col-sm-12 col-xs-12'>\n";	

/////////////////////////////////////////////////////////////////////////////////////////////////	
// YOUR LAST FIGURE  ////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

		openside($locale['mc_0005']);
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
							echo "<div class='side-small'>".$locale['mc_0009']."
							<a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>".trimlink($data['figure_title'], 10)." [".trimlink($data['figure_manufacturer_name'], 15)."]</a></div>";
						}
					} else {	
						echo $locale['mc_0010'];
					}

		closeside();
		echo "</div>";
/////////////////////////////////////////////////////////////////////////////////////////////////		
// PANEL OF ALL FIGURE FROM USER  ///////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

		echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";

		openside($locale['yours']);
	
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
			
			//$info['page_nav'] = $max_rows > $figurelibSettings['figure_per_page'] ? makepagenav($_GET['rowstart'], $figurelibSettings['figure_per_page'], $max_rows, 3, INFUSIONS."figurelib/mycollection.php?figure_id=".$_GET['figure_id']."&amp;") : 0;
			
			//$info['page_nav'] = ($max_rows > $figurelibSettings['figure_per_page']) ? makepagenav($_GET['rowstart'], $figurelibSettings['figure_per_page'], $max_rows, 3, FUSION_SELF."?&amp;") : "";

		if (dbrows($result) > 0) {				

				// WENN DATEN UNGLEICH = 0 DANN DARSTELLUNG DER DATEN
	 		
				echo "<hr>";
											
				echo "<div class='navbar-default'>";
				echo "<div class='container-fluid'>\n";
				echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";											
						// COLUMN 1 (image)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_018']."</div>\n";
						echo "</div>\n";

						// COLUMN 2 (name of figure)
						echo "<div class='col-lg-3 col-md-3 col-sm-4 col-xs-4'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_002']."</div>\n";
						echo "</div>\n";						
						
						// COLUMN 3 (manufacturer)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_003']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 4 (brand)
						echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_004']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 5 (scale)
						echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_005']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 6 (release date)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_006']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 7 (rating)
						echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_010']."</div>\n";
						echo "</div>\n";						
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				
				echo "<hr>";
		 
		 		echo "<div class='container-fluid'>\n";
				echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	
		 
			while($data = dbarray($result)){
			
							// COLUMN 1 (image clickable)
							echo "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'>\n";
								echo "<div class='side-small figurelib-inforow-height'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>\n<img src='".figures_getImagePath("figure", "thumb2", $data['figure_id'])."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' /></a>";
							echo "</div></div>\n";					
							
						// COLUMN 2 (name of figure)
							echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
									echo "<div class='side-small figurelib-inforow-height'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."'>".trimlink($data['figure_title'], 10)."</a>";
									echo "</div></div>\n";	

						// COLUMN 3 (manufacturer)
							echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
									echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_003']." : ".$data['figure_manufacturer_name']."' alt='".$locale['CLFP_003']." : ".$data['figure_manufacturer_name']."'>".trimlink($data['figure_manufacturer_name'],10)."</div>\n";
									echo "</div>\n";
						
						// COLUMN 4 (brand)
							echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";
									echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_004']." : ".$data['figure_brand_name']."' alt='".$locale['CLFP_004']." : ".$data['figure_brand_name']."'>".trimlink($data['figure_brand_name'],10)."</div>\n";
									echo "</div>\n";
						
						// COLUMN 5 (scale)
							echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";
									echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_005']." : ".$data['figure_scale_name']."' alt='".$locale['CLFP_005']." : ".$data['figure_scale_name']."'>".trimlink($data['figure_scale_name'],6)."</div>\n";
									echo "</div>\n";
			
						// No release date or unknown = "no data" / WENN KEIN WERT ZUM DATUM IN DB DANN ZEIGE HINWEIS "NO DATA"
						if ($data['figure_pubdate'] == "") {
							
									// COLUMN 6 (release date)
									echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";
										echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_006']." : ".$locale['CLFP_008']."' alt='".$locale['CLFP_006']." : ".$locale['CLFP_008']."'>".trimlink($locale['CLFP_008'],6)."</div>\n";
									echo "</div>\n";			
						} else {
							
									echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";
										echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_006']." : ".$data['figure_pubdate']."' alt='".$locale['CLFP_006']." : ".$data['figure_pubdate']."'>".trimlink($data['figure_pubdate'],12)."</div>\n";
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
						
						echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_010']."' alt='".$locale['CLFP_010']."'>".$rating."</div>\n";
						echo "</div>\n";
  			}	
				echo "</div>\n";
				echo "</div>\n";	
				echo "</div>\n";		
			
				//echo "<hr>\n";
				
				// PAGE NAV
				echo $info['page_nav'] ? "<div class='text-right'>".$info['page_nav']."</div>" : '';
				
				echo "<hr>\n";
					
					if (iADMIN || iSUPERADMIN) {		
																	
						echo "<div class='navbar-default'>";
						echo "<div class='container-fluid'>\n";
						echo "<div class='table-responsive'>\n";
						echo "<div class='row'>\n";						
								// ['CLFP_016']." = "Admin"
								echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
								echo "<div align='center'><a href='".INFUSIONS.'figurelib/admin.php'.$aidlink."'>".$locale['CLFP_016']."</a>";
								echo "</div></div>\n";
						echo "</div>\n";
						echo "</div>\n";
						echo "</div>\n";
						echo "</div>\n";
						
				echo "<hr>\n";
					}												
		}
	
		} else {
			
						echo "<div style='text-align: center;'>".$locale['CLFP_001']."</div>"; // 001 = No figures available"
		
		}
		
		echo "</div>";
		closeside();
						
		
	} else {
						$locale['mc_0001']= "My Figure Collection";
						$locale['mc_0011']= "This feature is only available for registered members. Please Sign up ";
						$locale['mc_0012']= "HERE";
						
		openside($locale['mc_0001']);
						echo $locale['mc_0011'];
						echo "<a href='".BASEDIR."register.php'>".$locale['mc_0012']."</a>";
		closeside();
	}
		closetable();
		


	
require_once THEMES."templates/footer.php";
