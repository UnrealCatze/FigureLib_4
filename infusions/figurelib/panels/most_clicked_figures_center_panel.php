<?php
/***************************************************************************
 *   MOST CLICKED FIGURES CENTER PANEL                                     *
 *                                                                         *
 *   Copyright (C) 2016 Catzenjaeger                                       *
 *   www.aliencollectors.com                                               *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 ***************************************************************************/
require_once "../../../maincore.php";
require_once THEMES."templates/header.php";
global $aidlink;
include INFUSIONS."figurelib/infusion_db.php";
require_once INCLUDES."infusions_include.php";

// CENTER PANEL
$locale['CLFP_000']= "Alien Figures Database";
$locale['CLFP_001']= "No figures found";
$locale['CLFP_002']= "Name";
$locale['CLFP_003']= "Manufacturer";
$locale['CLFP_004']= "Brand";
$locale['CLFP_005']= "Scale";
$locale['CLFP_006']= "Year";
$locale['CLFP_007']= "Image";
$locale['CLFP_008']= "No Data";
$locale['CLFP_009']= "X";
$locale['CLFP_010']= "Rating";
$locale['CLFP_011']= "Submitter";
$locale['CLFP_012']= "Count";
$locale['CLFP_013']= "CATEGORIES";
$locale['CLFP_014']= "SUBMIT FIGURE";
$locale['CLFP_015']= "MOST VIEWED";
$locale['CLFP_016']= "ADMIN";
$locale['CLFP_017']= "MY COLLECTION";
$locale['CLFP_018']= "IMAGE";

$locale['lastentries']= "Last Figures";	
	


		// DATENBANK AUSLESEN UND DATEN BEREITSTELLEN

	$result = dbquery(
			"SELECT tb.figure_id, tb.figure_submitter, tb.figure_clickcount, tb.figure_freigabe, tb.figure_pubdate, tb.figure_scale, tb.figure_title, tb.figure_manufacturer, tb.figure_brand, tb.figure_datestamp, tb.figure_cat, tbc.figure_cat_id, tbc.figure_cat_name, tbu.user_id, tbu.user_name, tbu.user_status, tbu.user_avatar, tbm.figure_manufacturer_name, tbb.figure_brand_name, tby.figure_year_id, tby.figure_year, tbs.figure_scale_id, tbs.figure_scale_name FROM ".DB_FIGURE_ITEMS." tb
			LEFT JOIN ".DB_USERS." tbu ON tb.figure_submitter=tbu.user_id
			INNER JOIN ".DB_FIGURE_CATS." tbc ON tb.figure_cat=tbc.figure_cat_id
			INNER JOIN ".DB_FIGURE_MANUFACTURERS." tbm ON tbm.figure_manufacturer_id = tb.figure_manufacturer
			INNER JOIN ".DB_FIGURE_BRANDS." tbb ON tbb.figure_brand_id = tb.figure_brand
			INNER JOIN ".DB_FIGURE_SCALES." tbs ON tbs.figure_scale_id = tb.figure_scale
			INNER JOIN ".DB_FIGURE_YEARS." tby ON tby.figure_year_id = tb.figure_pubdate
			".(multilang_table("FI") ? "WHERE figure_language='".LANGUAGE."' AND" : "WHERE")." tb.figure_freigabe='1' ORDER BY figure_clickcount DESC LIMIT 0,10");	
	
		
		// PANEL ÖFFNEN / ANFANG		
 
 //openside($locale['lastentries']);
 	 
		// WENN DATEN UNGLEICH = 0 DANN DARSTELLUNG DER DATEN
		if (dbrows($result) != 0) {
		 						
				echo "<div class='row'>\n";	
				echo "<div class='container-fluid'>\n";
				echo "<div class='table-responsive'>\n";
				echo "<hr>";
						
						// COLUMN 1 (image)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'>".$locale['CLFP_018']."</div>\n";
						echo "</div></div>\n";

						// COLUMN 2 (name of figure)
						echo "<div class='col-lg-3 col-md-3 col-sm-4 col-xs-4'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'>".$locale['CLFP_002']."</div>\n";
						echo "</div></div>\n";						
						
						// COLUMN 3 (manufacturer)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'>".$locale['CLFP_003']."</div>\n";
						echo "</div></div>\n";
						
						// COLUMN 4 (brand)
						echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'>".$locale['CLFP_004']."</div>\n";
						echo "</div></div>\n";
						
						// COLUMN 5 (scale)
						echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'>".$locale['CLFP_005']."</div>\n";
						echo "</div></div>\n";
						
						// COLUMN 6 (release date)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'>".$locale['CLFP_006']."</div>\n";
						echo "</div></div>\n";
						
						// COLUMN 7 (rating)
						echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'>".$locale['CLFP_010']."</div>\n";
						echo "</div></div>\n";
						
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				
				echo "<hr>";
		 
		while($data = dbarray($result)){
									echo "<div class='row'>\n";	
									echo "<div class='container-fluid'>\n";
									echo "<div class='table-responsive'>\n";
						
						// WHILE SCHLEIFE FÜR DAS HOLEN DES BILDES AUS ORDNER / ORDNER MUSS IN infusion.db.php deklariert sein!
				
						$result2 = dbquery("SELECT
						   figure_images_image_id,
						   figure_images_image,
						   figure_images_thumb
						FROM ".DB_FIGURE_IMAGES."
						WHERE figure_images_figure_id='".$data['figure_id']."' LIMIT 0,1");
 
						// Fragen, ob überhaupt ein Ergebnis kommt
						if(dbrows($result2)){
				 
							while($data2 = dbarray($result2)){
									
									
									
									
									// COLUMN 1 (image clickable)
									echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";
										echo "<div class='side-small'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>\n<img src='". THUMBS_FIGURES.$data2['figure_images_thumb'] ."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' style='border:0px;max-height:20px;max-width:20px'/></a>";
									echo "</div></div>\n";					
							}
						
						} else { 
									
									echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";
										echo "<div class='side-small'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>\n<img src='".($data2['figure_images_thumb'] ? THUMBS_FIGURES.$data2['figure_images_thumb'] : INFUSIONS.$inf_folder."/images/default.png")."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' style='border:0px;max-height:20px;max-width:20px'/></a>";
									echo "</div></div>\n";				
							
								}	

						// COLUMN 2 (name of figure)
						echo "<div class='col-lg-3 col-md-3 col-sm-4 col-xs-4'>\n";
							echo "<div class='side-small'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."'>".trimlink($data['figure_title'], 10)."</a>";
						echo "</div></div>\n";	

						// COLUMN 3 (manufacturer)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
							echo "<div class='side-small' title='".$locale['CLFP_003']." : ".$data['figure_manufacturer_name']."' alt='".$locale['CLFP_003']." : ".$data['figure_manufacturer_name']."'>".trimlink($data['figure_manufacturer_name'],10)."</div>\n";
						echo "</div>\n";
						
						// COLUMN 4 (brand)
						echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='side-small' title='".$locale['CLFP_004']." : ".$data['figure_brand_name']."' alt='".$locale['CLFP_004']." : ".$data['figure_brand_name']."'>".trimlink($data['figure_brand_name'],10)."</div>\n";
						echo "</div>\n";
						
						// COLUMN 5 (scale)
						echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";
							echo "<div class='side-small' title='".$locale['CLFP_005']." : ".$data['figure_scale_name']."' alt='".$locale['CLFP_005']." : ".$data['figure_scale_name']."'>".trimlink($data['figure_scale_name'],7)."</div>\n";
						echo "</div>\n";
			
						// No release date or unknown = "no data" / WENN KEIN WERT ZUM DATUM IN DB DANN ZEIGE HINWEIS "NO DATA"
						if ($data['figure_pubdate'] == "") {
								
										// COLUMN 6 (release date)
										echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";
											echo "<div class='side-small' title='".$locale['CLFP_006']." : ".$locale['CLFP_008']."' alt='".$locale['CLFP_006']." : ".$locale['CLFP_008']."'>".$locale['CLFP_008']."</div>\n";
										echo "</div>\n";			
						} else {
								
										echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";
											echo "<div class='side-small' title='".$locale['CLFP_006']." : ".$data['figure_year']."' alt='".$locale['CLFP_006']." : ".$data['figure_year']."'>".$data['figure_year']."</div>\n";
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
							echo "<div class='side-small' title='".$locale['CLFP_010']."' alt='".$locale['CLFP_010']."'>".$rating."</div>\n";
						echo "</div>\n";
  
				
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				
		}
				echo "<hr>\n";

					if (iADMIN || iSUPERADMIN) {		
										
								echo "<div class='row'>\n";	
								echo "<div class='container-fluid'>\n";
								echo "<div class='table-responsive'>\n";				
										// ['CLFP_016']." = "Admin"
											echo "<div align='center'><div class='btn btn-default btn-block btn-xs'>
												  <a href='".INFUSIONS.'figurelib/admin.php'.$aidlink."'>".$locale['CLFP_016']."</a>
												  </div></div>\n";
								echo "</div>\n";
								echo "</div>\n";
								echo "</div>\n";
								echo "<hr>\n";
						
					} 
			

 } else {
			echo "<div style='text-align: center;'>".$locale['CLFP_001']."</div>"; // 001 = No figures available"
 }
require_once THEMES."templates/footer.php";
