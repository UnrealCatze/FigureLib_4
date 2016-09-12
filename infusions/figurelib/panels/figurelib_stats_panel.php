<?php
/***************************************************************************
 *   figure_stats_panel.php for FIGURELIB                                  *
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
				
// Hande MyCollection Actions
if (iMEMBER) {
	
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
}				

// If Infusion isn't installed, redirect to Errorpage
if (!db_exists(DB_FIGURE_ITEMS)) { redirect(BASEDIR."error.php?code=404"); }

// Get Settings
$fil_settings = get_settings("figurelib");
		
// Start Site
opentable("<div class='text-bold'>".$locale['stats_001']."</div>");

###########################################################################################################################
#################### GESAMTZAHL ALLER FIGUREN + Gesamtzahl Figuren in Userbesitz        ###################################
###########################################################################################################################

			openside("");			
				echo "<div class='row'>\n";
				
			// GESAMTZAHL ALLER FIGUREN /////////////////////////////////////////		
				echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";														
					echo "<div class='navbar-default text-bold'>";	
						//['stats_004'] ="Total number of figures:";
						echo "<span>".$locale['stats_004']."</span>\n";
					echo "</div>\n";
				echo "</div>\n";
						
				echo "<div class='col-lg-2 col-md-2 col-sm-12 col-xs-12'>\n";
						$allFiguresCounter = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe='1'");
						//echo "<span class='badge'>".$allFiguresCounter."</span>\n";	
						echo "<button class='btn btn-primary btn-xs' type='button'><span class='badge'>".$allFiguresCounter."</button>\n";
				echo "</div>\n";

			// Gesamtzahl Figuren in Userbesitz //////////////////////////////////	
				echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";																		
					echo "<div class='navbar-default text-bold'>";	
						//['stats_005'] ="Total figures owned by user:";
						echo "<span>".$locale['stats_005']."</span>\n";
					echo "</div>\n";
					echo "</div>\n";
				
				
					echo "<div class='col-lg-2 col-md-2 col-sm-12 col-xs-12'>\n";								
							$allFiguresCounterUser = dbcount("(figure_userfigures_id)",  DB_FIGURE_USERFIGURES, "");
							//echo "<span class='badge'>".$allFiguresCounterUser."</span>\n";	
							echo "<button class='btn btn-primary btn-xs' type='button'><span class='badge'>".$allFiguresCounterUser."</button>\n";							
				echo "</div>\n";
			//////////////////////////////////////////////////////////////////////				
				
				echo "</div>\n";
			closeside();	
closetable();
###########################################################################################################################
############################################### TEUERSTE FIGUR  ###########################################################
###########################################################################################################################			
opentable("<div class='text-bold'>".$locale['stats_007']."</div>");

	echo "<hr>";	
 echo "<div class='panel-group' id='accordion'>
  <div class='panel panel-default'>
    <div class='panel-heading'>
      <h4 class='panel-title'>
        <a data-toggle='collapse' data-parent='#accordion' href='#collapse1'>".$locale['CLFP_027']."</a>
      </h4>
    </div>
    <div id='collapse1' class='panel-collapse collapse in'>
      <div class='panel-body'>";
	  
	  // SEETING FIGURES PER SITE
		$limitteuerste = 10; 
		
		// Set Empty Arrays
		$info = array("figure_rows" => 0, "page_nav" => false);
		$info['item'] = array();

		// Count all Figures
		$max_rows = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe=1 
									AND ".groupaccess("figure_visibility")." 
									AND figure_retailprice != ''");
		
		// Check Rowstart
		$_GET['rowstart'] = isset($_GET['rowstart']) && isnum($_GET['rowstart']) && $_GET['rowstart'] <= $max_rows ? $_GET['rowstart'] : 0;	
	
	if ($max_rows) {
	  
					$resultteuerste = dbquery("
                     SELECT
                        tb.figure_id, tb.figure_submitter, tb.figure_retailprice, tb.figure_usedprice, tb.figure_freigabe, tb.figure_pubdate, tb.figure_scale, tb.figure_title, tb.figure_manufacturer, tb.figure_brand, tb.figure_datestamp, tb.figure_cat,
                        tbc.figure_cat_id, tbc.figure_cat_name,
                        tbu.user_id, tbu.user_name, tbu.user_status, tbu.user_avatar,
                        tbm.figure_manufacturer_name,
                        tbb.figure_brand_name,                     
                        tbs.figure_scale_id, tbs.figure_scale_name,                     
                        fuf.figure_userfigures_figure_id, fuf.figure_userfigures_user_id
                     FROM ".DB_FIGURE_ITEMS." AS tb
                     LEFT JOIN ".DB_USERS." AS tbu ON tb.figure_submitter=tbu.user_id
                     LEFT JOIN ".DB_FIGURE_USERFIGURES." AS fuf ON fuf.figure_userfigures_figure_id=tb.figure_id
                     LEFT JOIN ".DB_FIGURE_CATS." AS tbc ON tb.figure_cat=tbc.figure_cat_id
                     LEFT JOIN ".DB_FIGURE_MANUFACTURERS." AS tbm ON tbm.figure_manufacturer_id = tb.figure_manufacturer
                     LEFT JOIN ".DB_FIGURE_BRANDS." AS tbb ON tbb.figure_brand_id = tb.figure_brand
                     LEFT JOIN ".DB_FIGURE_SCALES." AS tbs ON tbs.figure_scale_id = tb.figure_scale
                     WHERE ".(multilang_table("FI") ? "tb.figure_language='".LANGUAGE."' AND" : "")." tb.figure_freigabe='1'
						AND figure_retailprice != ''	
						GROUP BY tb.figure_id
						ORDER BY tb.figure_retailprice DESC
						LIMIT ".$_GET['rowstart'].",".$limitteuerste."					
                  ");
				 
				 // Pagenav		
			  $info['page_nav'] = $max_rows > $limitteuerste ? makepagenav($_GET['rowstart'], $limitteuerste, $max_rows, 3, FUSION_SELF."?") : 0;
			  

	  
	// WENN DATEN UNGLEICH = 0 DANN DARSTELLUNG DER DATEN
							
		 if (dbrows($resultteuerste) == 0) {
			
			openside("");
			
					echo "<div class='row'>\n";
					echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
					//['CLFP_024']= "Not enough data to create the statistics available.";
					echo "<div style='text-align: center;'>".$locale['CLFP_024']."</div>"; 
					echo "</div>\n";
					echo "</div>\n";
			
			closeside();
			
		} else {
			
			openside("");				  
			 			 
				/*//// GRID SYSTEM FOR PANEL AS OVERVIEW ///////////////////////////////////////
					echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";		image
					echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";		title
					echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";		manufacturer
					echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";	brand
					echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";	year
					echo "<div class='col-lg-2 col-md-2 hidden-sm hidden-xs'>\n";	retailprice
					echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";		edit (only for admins)
					echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";		add/remove
				//////////////////////////////////////////////////////////////////////////////*/
		
				echo "<div class='navbar-default text-bold'>";	
				echo "<div class='text-bold'>";				
				echo "<div class='container-fluid'>\n";
				//echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	
				
		
						// COLUMN 1 (image)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_018']."</div>\n";
						echo "</div>\n";

						// COLUMN 2 (name of figure)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
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
						
						// COLUMN 5 (release date)
						echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_006']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 6 (retailprice) 
						echo "<div class='col-lg-2 col-md-2 hidden-sm hidden-xs text-danger'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['figure_449']."</div>\n";
						echo "</div>\n";
						
					if (iADMIN || iSUPERADMIN) {
						// COLUMN 7 (edit) only for admins
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>Edit</div>\n";
						echo "</div>\n";
					} else {
						
					}
												
						// COLUMN 8 Usercount (only for Guests and members - admins will see edit button)if (iADMIN || iSUPERADMIN) {
					if (iADMIN || iSUPERADMIN) {
					} else {
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>Count</div>\n";
						echo "</div>\n";
					}
						
						// COLUMN 8 (action)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>Opt.</div>\n";
						echo "</div>\n";
											
				echo "</div>\n";
				//echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				
				echo "<hr>";
		 
	while($data = dbarray($resultteuerste)){
									
				echo "<div class='container-fluid'>\n";
				//echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	
				
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
												
						// COLUMN 1 (image clickable)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='side-small'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>\n<img src='".$imageURL."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' style='border:0px;max-height:40px;max-width:40px'/></a>";
						echo "</div></div>\n";				

						// COLUMN 2 (name of figure)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
							echo "<div class='side-small'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."'>".trimlink($data['figure_title'], 10)."</a>";
							echo "</div></div>\n";	
		
						// COLUMN 3 (manufacturer)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";	
							echo "<div class='side-small' title='".$locale['CLFP_003']." : ".$data['figure_manufacturer_name']."' alt='".$locale['CLFP_003']." : ".$data['figure_manufacturer_name']."'>".trimlink($data['figure_manufacturer_name'],10)."</div>\n";
						echo "</div>\n";
						
						// COLUMN 4 (brand)
						echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='side-small' title='".$locale['CLFP_004']." : ".$data['figure_brand_name']."' alt='".$locale['CLFP_004']." : ".$data['figure_brand_name']."'>".trimlink($data['figure_brand_name'],15)."</div>\n";
						echo "</div>\n";
			
						// COLUMN 5 (release date)
						// No release date or unknown = "no data" / WENN KEIN WERT ZUM DATUM IN DB DANN ZEIGE HINWEIS "NO DATA"
						if ($data['figure_pubdate'] == "") {
							
								echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'><div class='side-small' title='".$locale['CLFP_006']." : ".$locale['CLFP_008']."' alt='".$locale['CLFP_006']." : ".$locale['CLFP_008']."'>".trimlink($locale['CLFP_008'],5)."</div></div>\n";
								} else {
								echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'><div class='side-small' title='".$locale['CLFP_006']." : ".$data['figure_pubdate']."' alt='".$locale['CLFP_006']." : ".$data['figure_pubdate']."'>".trimlink($data['figure_pubdate'],7)."</div></div>\n";
								} 
		
						// COLUMN 6 (retailprice)														
						echo "<div class='col-lg-2 col-md-2 hidden-sm hidden-xs'>\n";
							echo "<div class='side-small text-danger' title='".$locale['figure_449']." : ".$data['figure_retailprice']." ($)' alt='".$locale['figure_449']."'>".TRIMLINK($data['figure_retailprice'],6)."</div>\n";
						echo "</div>\n";
						
						// COLUMN 7 (edit) only for admins
						
						if (iADMIN || iSUPERADMIN) {
						echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";	
													
							global $aidlink;
							$settings = fusion_get_settings();
								// ['cifg_0005'] = "Edit";
								
								echo "<a class='' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_form&amp;action=edit&amp;figure_id=".$data['figure_id']."'><span class='fa fa-pencil-square-o' aria-hidden='true'></span></a>"; 
							echo "</div>\n";
							} else {
								
								// SHOW NOTHING - ITS ONLY FOR ADMINS
						
							}
						
						// COLUMN 8 Usercount
						if (iADMIN || iSUPERADMIN) {	
							
							// nicht zu sehen für admins
							
							} else {
													
									echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
									$count = dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."'");	
							
										echo "<div class='side-small' title='".$locale['CLFP_020']." : ".$count."' alt='".$locale['CLFP_020']." : ".$count."'>".trimlink($count,6)."</div>\n";
									echo "</div>\n";	
							}		
							
						// COLUMN 8 (add/remove)
						echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";	
	
							// MyCollection
							if (iMEMBER) {
										
								// Check if User has it in his Collection
								echo openform("collectionform", "post", FUSION_SELF);
								if (dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'")) {
									echo form_button("remove_figure", "", $data['figure_id'], ["class" => "btn-xs btn-success", "alt" => $locale['userfigure_002'], "icon" => "fa fa-fw fa-check-square-o"]);
								} else {
									echo form_button("add_figure", "", $data['figure_id'], ["class" => "btn-xs btn-default", "alt" => $locale['userfigure_001'], "icon" => "fa fa-fw fa-plus-square-o"]);
								}
								echo closeform();
												
							} else {
								// $locale['mc_0011']= "This feature is only available for registered members. Please Sign up ";
								echo "<span alt='".$locale['userfigure_001']." | ".$locale['mc_0011']."' title='".$locale['userfigure_001']." | ".$locale['mc_0011']."' class='fa fa-eye-slash' aria-hidden='true'></span>";
							}						
						echo "</div>\n";
				
				echo "</div>\n";
				//echo "</div>\n";
				echo "</div>\n";	
				
	}
	// PAGE NAV
		echo $info['page_nav'] ? "<div class='text-right'>".$info['page_nav']."</div>" : '';
 closeside();
 
 }  
	}   
	  
	  
	  
	  echo "</div>
    </div>
  </div>";
  
        
#######################################################################################################################	
######################################## HÖCHSTER GERBAUCHTMARKTPREIS #################################################
#######################################################################################################################


echo "<div class='panel panel-default'>
    <div class='panel-heading'>
      <h4 class='panel-title'>";
        echo "<a data-toggle='collapse' data-parent='#accordion' href='#collapse2'>".$locale['CLFP_026']."</a>
      </h4>
    </div>
    <div id='collapse2' class='panel-collapse collapse'>
      <div class='panel-body'>";	
				
		// SEETING FIGURES PER SITE
		$limitteuerstegebraucht = 10; 
		
		// Set Empty Arrays
		$info = array("figure_rows" => 0, "page_nav" => false);
		$info['item'] = array();

		// Count all Figures
		$max_rows1 = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe=1 
									AND ".groupaccess("figure_visibility")."
									AND figure_usedprice != ''");
		
		// Check Rowstart
		$_GET['rowstart'] = isset($_GET['rowstart']) && isnum($_GET['rowstart']) && $_GET['rowstart'] <= $max_rows1 ? $_GET['rowstart'] : 0;	
	
	if ($max_rows1) {	
				
				$resultteuerstegebraucht = dbquery("
                     SELECT
                        tb.figure_id, tb.figure_submitter, tb.figure_retailprice, tb.figure_usedprice, tb.figure_freigabe, tb.figure_pubdate, tb.figure_scale, tb.figure_title, tb.figure_manufacturer, tb.figure_brand, tb.figure_datestamp, tb.figure_cat,
                        tbc.figure_cat_id, tbc.figure_cat_name,
                        tbu.user_id, tbu.user_name, tbu.user_status, tbu.user_avatar,
                        tbm.figure_manufacturer_name,
                        tbb.figure_brand_name,                     
                        tbs.figure_scale_id, tbs.figure_scale_name,                     
                        fuf.figure_userfigures_figure_id, fuf.figure_userfigures_user_id
                     FROM ".DB_FIGURE_ITEMS." AS tb
                     LEFT JOIN ".DB_USERS." AS tbu ON tb.figure_submitter=tbu.user_id
                     LEFT JOIN ".DB_FIGURE_USERFIGURES." AS fuf ON fuf.figure_userfigures_figure_id=tb.figure_id
                     LEFT JOIN ".DB_FIGURE_CATS." AS tbc ON tb.figure_cat=tbc.figure_cat_id
                     LEFT JOIN ".DB_FIGURE_MANUFACTURERS." AS tbm ON tbm.figure_manufacturer_id = tb.figure_manufacturer
                     LEFT JOIN ".DB_FIGURE_BRANDS." AS tbb ON tbb.figure_brand_id = tb.figure_brand
                     LEFT JOIN ".DB_FIGURE_SCALES." AS tbs ON tbs.figure_scale_id = tb.figure_scale
                     WHERE ".(multilang_table("FI") ? "tb.figure_language='".LANGUAGE."' AND" : "")." tb.figure_freigabe='1'
						AND figure_usedprice != ''
						GROUP BY tb.figure_id
						ORDER BY tb.figure_usedprice DESC
						LIMIT ".$_GET['rowstart'].",".$limitteuerstegebraucht."		
						
                  ");	

				// Pagenav		
			  $info['page_nav'] = $max_rows1 > $limitteuerstegebraucht ? makepagenav($_GET['rowstart'], $limitteuerstegebraucht, $max_rows1, 3, FUSION_SELF."?") : 0;
			  				  
								
    if (dbrows($resultteuerstegebraucht) == 0) {
		
					openside("");
								
					echo "<div class='row'>\n";
					echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
					//['CLFP_024']= "Not enough data to create the statistics available.";
					echo "<div style='text-align: center;'>".$locale['CLFP_024']."</div>"; 
					echo "</div>\n";
					echo "</div>\n";
			
			closeside();
			
		} else {
								
		openside("");				
		 	
				/*//// GRID SYSTEM FOR PANEL AS OVERVIEW ///////////////////////////////////////
					echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";		image
					echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";		title
					echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";		manufacturer
					echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";	brand
					echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";	year
					echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";	usedprice
					echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";		edit (only for admins)
					echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";		add/remove
				//////////////////////////////////////////////////////////////////////////////*/
								
				echo "<div class='navbar-default text-bold'>";	
				echo "<div class='text-bold'>";				
				echo "<div class='container-fluid'>\n";
				//echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	
		
						// COLUMN 1 (image)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_018']."</div>\n";
						echo "</div>\n";

						// COLUMN 2 (name of figure)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_002']."</div>\n";
						echo "</div>\n";					
						
						// COLUMN 3 (manufacturer)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";	
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_003']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 4 (usedprice)
						echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_004']."</div>\n";
						echo "</div>\n";
										
						// COLUMN 6 (release date)
						echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_006']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 7 (usedprice)
						echo "<div class='col-lg-2 col-md-2 hidden-sm hidden-xs text-danger'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['figure_456']."</div>\n";
						echo "</div>\n";
						
						if (iADMIN || iSUPERADMIN) {
						// COLUMN 8 (edit) only for admins
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>Edit</div>\n";
						echo "</div>\n";
						} else {}
						
						
						// COLUMN 9 Usercount (only for Guests and members - admins will see edit button)if (iADMIN || iSUPERADMIN) {
							if (iADMIN || iSUPERADMIN) {
								} else {
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>Count</div>\n";
						echo "</div>\n";
						}
						
						// COLUMN 9 (action)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>Opt.</div>\n";
						echo "</div>\n";
											
				echo "</div>\n";
				//echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				
				echo "<hr>";
		 
		while ($data = dbarray($resultteuerstegebraucht)) {	
				
						
				echo "<div class='container-fluid'>\n";
				//echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	
				
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
												
						// COLUMN 1 (image clickable)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='side-small'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>\n<img src='".$imageURL."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' style='border:0px;max-height:40px;max-width:40px'/></a>";
						echo "</div></div>\n";				

						// COLUMN 2 (name of figure)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
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
									
						// COLUMN 5 (release date)
						// No release date or unknown = "no data" / WENN KEIN WERT ZUM DATUM IN DB DANN ZEIGE HINWEIS "NO DATA"
						if ($data['figure_pubdate'] == "") {
							
								echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'><div class='side-small' title='".$locale['CLFP_006']." : ".$locale['CLFP_008']."' alt='".$locale['CLFP_006']." : ".$locale['CLFP_008']."'>".trimlink($locale['CLFP_008'],5)."</div></div>\n";
								} else {
								echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'><div class='side-small' title='".$locale['CLFP_006']." : ".$data['figure_pubdate']."' alt='".$locale['CLFP_006']." : ".$data['figure_pubdate']."'>".trimlink($data['figure_pubdate'],7)."</div></div>\n";
								} 
		
						// COLUMN 6 (usedprice)
														
						echo "<div class='col-lg-2 col-md-2 hidden-sm hidden-xs'>\n";
							echo "<div class='side-small text-danger' title='".$locale['figure_456']." : ".$data['figure_usedprice']." ($)' alt='".$locale['figure_456']."'>".TRIMLINK($data['figure_usedprice'],6)."</div>\n";
						echo "</div>\n";
						
						// COLUMN 7 (edit) only for admins
						
						if (iADMIN || iSUPERADMIN) {
						echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";	
													
							global $aidlink;
							$settings = fusion_get_settings();
								// ['cifg_0005'] = "Edit";
								
								echo "<a class='' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_form&amp;action=edit&amp;figure_id=".$data['figure_id']."'><span class='fa fa-pencil-square-o' aria-hidden='true'></span></a>"; 
							echo "</div>\n";
							} else {
								
								// SHOW NOTHING - ITS ONLY FOR ADMINS
						
							}
						
						// COLUMN 8 Usercount
						if (iADMIN || iSUPERADMIN) {	
							
							// nicht zu sehen für admins
							
							} else {
													
									echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
									$count = dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."'");	
							
										echo "<div class='side-small' title='".$locale['CLFP_020']." : ".$count."' alt='".$locale['CLFP_020']." : ".$count."'>".trimlink($count,6)."</div>\n";
									echo "</div>\n";	
							}		
							
						// COLUMN 9 (add/remove)
						echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";	
	
							// MyCollection
							if (iMEMBER) {
										
								// Check if User has it in his Collection
								echo openform("collectionform", "post", FUSION_SELF);
								if (dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'")) {
									echo form_button("remove_figure", "", $data['figure_id'], ["class" => "btn-xs btn-success", "alt" => $locale['userfigure_002'], "icon" => "fa fa-fw fa-check-square-o"]);
								} else {
									echo form_button("add_figure", "", $data['figure_id'], ["class" => "btn-xs btn-default", "alt" => $locale['userfigure_001'], "icon" => "fa fa-fw fa-plus-square-o"]);
								}
								echo closeform();
												
							} else {
								// $locale['mc_0011']= "This feature is only available for registered members. Please Sign up ";
								echo "<span alt='".$locale['userfigure_001']." | ".$locale['mc_0011']."' title='".$locale['userfigure_001']." | ".$locale['mc_0011']."' class='fa fa-eye-slash' aria-hidden='true'></span>";
							}						
						echo "</div>\n";
						
				echo "</div>\n";
				//echo "</div>\n";
				echo "</div>\n";
						
		}
		// PAGE NAV
		echo $info['page_nav'] ? "<div class='text-right'>".$info['page_nav']."</div>" : '';
		closeside();
    }	

 
 }  

	
	
		echo "</div>";
	    echo "</div>";
	echo "</div>\n";

###################################################################################################################
###################### MOST POINT OF ARTICULATION #################################################################
###################################################################################################################
  
   echo "<div class='panel panel-default'>
    <div class='panel-heading'>
      <h4 class='panel-title'>
        <a data-toggle='collapse' data-parent='#accordion' href='#collapse3'>".$locale['figure_1305']."</a>
      </h4>
    </div>
    <div id='collapse3' class='panel-collapse collapse'>
      <div class='panel-body'>";
	  
	  	// SEETING FIGURES PER SITE
		$limitmostPOA = 10; 
		
		// Set Empty Arrays
		$info = array("figure_rows" => 0, "page_nav" => false);
		$info['item'] = array();

		// Count all Figures
		$max_rows2 = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe=1 
															  AND ".groupaccess("figure_visibility")."
															  AND figure_poa != '1'
															  AND figure_poa != '2'
															  ");
		
		// Check Rowstart
		$_GET['rowstart'] = isset($_GET['rowstart']) && isnum($_GET['rowstart']) && $_GET['rowstart'] <= $max_rows2 ? $_GET['rowstart'] : 0;	
	
	if ($max_rows2) {
	  
	 $resultmostPOA = dbquery("
                     SELECT
                        tb.figure_id, tb.figure_submitter, tb.figure_retailprice, tb.figure_usedprice, tb.figure_freigabe, tb.figure_pubdate, tb.figure_scale, tb.figure_title, tb.figure_poa, tb.figure_manufacturer, tb.figure_brand, tb.figure_datestamp, tb.figure_cat,
                        tbc.figure_cat_id, tbc.figure_cat_name,
                        tbu.user_id, tbu.user_name, tbu.user_status, tbu.user_avatar,
			fpoa.figure_poa_id, fpoa.figure_poa_name,
                        tbm.figure_manufacturer_name, tbb.figure_brand_name,                     
                        tbs.figure_scale_id, tbs.figure_scale_name, 					
                        fuf.figure_userfigures_figure_id, fuf.figure_userfigures_user_id
                     FROM ".DB_FIGURE_ITEMS." AS tb
                     LEFT JOIN ".DB_USERS." AS tbu ON tb.figure_submitter=tbu.user_id
                     LEFT JOIN ".DB_FIGURE_USERFIGURES." AS fuf ON fuf.figure_userfigures_figure_id=tb.figure_id
		     LEFT JOIN ".DB_FIGURE_POAS." AS fpoa ON fpoa.figure_poa_id=tb.figure_poa
                     LEFT JOIN ".DB_FIGURE_CATS." AS tbc ON tb.figure_cat=tbc.figure_cat_id
                     LEFT JOIN ".DB_FIGURE_MANUFACTURERS." AS tbm ON tbm.figure_manufacturer_id = tb.figure_manufacturer
                     LEFT JOIN ".DB_FIGURE_BRANDS." AS tbb ON tbb.figure_brand_id = tb.figure_brand
                     LEFT JOIN ".DB_FIGURE_SCALES." AS tbs ON tbs.figure_scale_id = tb.figure_scale
                     WHERE ".(multilang_table("FI") ? "tb.figure_language='".LANGUAGE."' 
						AND" : "")." tb.figure_freigabe='1'
						AND fpoa.figure_poa_name != '00 No articulation'  
						AND fpoa.figure_poa_name != 'Unknown'	 
						ORDER BY fpoa.figure_poa_name DESC
						LIMIT ".$_GET['rowstart'].",".$limitmostPOA."		
						
                  ");	

				// Pagenav		
			  $info['page_nav'] = $max_rows2 > $limitmostPOA ? makepagenav($_GET['rowstart'], $limitmostPOA, $max_rows2, 3, FUSION_SELF."?") : 0;
					
					
				
    if (dbrows($resultmostPOA) == 0) {
		
					openside("");
							
					echo "<div class='row'>\n";
					echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
					//['CLFP_024']= "Not enough data to create the statistics available.";
					echo "<div style='text-align: center;'>".$locale['CLFP_024']."</div>"; 
					echo "</div>\n";
					echo "</div>\n";
			
					closeside();
			
		} else {
		
						
		openside("");				

				/*//// GRID SYSTEM FOR PANEL AS OVERVIEW ///////////////////////////////////////
					echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";		image
					echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";		title
					echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";		manufacturer
					echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";	brand
					echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";	year
					echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";	poa
					echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";		edit (only for admins)
					echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";		add/remove
				//////////////////////////////////////////////////////////////////////////////*/
								
				echo "<div class='navbar-default text-bold'>";	
				echo "<div class='text-bold'>";				
				echo "<div class='container-fluid'>\n";
				//echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	
		
						// COLUMN 1 (image)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_018']."</div>\n";
						echo "</div>\n";

						// COLUMN 2 (name of figure)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
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
										
						// COLUMN 6 (release date)
						echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_006']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 7 (poa)
						echo "<div class='col-lg-2 col-md-2 hidden-sm hidden-xs text-danger'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['figure_1303']."</div>\n";
						echo "</div>\n";
						
						if (iADMIN || iSUPERADMIN) {
						// COLUMN 8 (edit) only for admins
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>Edit</div>\n";
						echo "</div>\n";
						} else {}
												
						// COLUMN 9 Usercount (only for Guests and members - admins will see edit button)if (iADMIN || iSUPERADMIN) {
							if (iADMIN || iSUPERADMIN) {
								} else {
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>Count</div>\n";
						echo "</div>\n";
						}
						
						// COLUMN 9 (action)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>Opt.</div>\n";
						echo "</div>\n";
											
				echo "</div>\n";
				//echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				
				echo "<hr>";
		 
		while ($data = dbarray($resultmostPOA)) {	
				
						
				echo "<div class='container-fluid'>\n";
				//echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	
				
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
												
						// COLUMN 1 (image clickable)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='side-small'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>\n<img src='".$imageURL."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' style='border:0px;max-height:40px;max-width:40px'/></a>";
						echo "</div></div>\n";				

						// COLUMN 2 (name of figure)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
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
									
						// COLUMN 5 (release date)
						// No release date or unknown = "no data" / WENN KEIN WERT ZUM DATUM IN DB DANN ZEIGE HINWEIS "NO DATA"
						if ($data['figure_pubdate'] == "") {
							
								echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'><div class='side-small' title='".$locale['CLFP_006']." : ".$locale['CLFP_008']."' alt='".$locale['CLFP_006']." : ".$locale['CLFP_008']."'>".trimlink($locale['CLFP_008'],5)."</div></div>\n";
								} else {
								echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'><div class='side-small' title='".$locale['CLFP_006']." : ".$data['figure_pubdate']."' alt='".$locale['CLFP_006']." : ".$data['figure_pubdate']."'>".trimlink($data['figure_pubdate'],7)."</div></div>\n";
								} 
		
						// COLUMN 6 (poa)
														
						echo "<div class='col-lg-2 col-md-2 hidden-sm hidden-xs'>\n";
							echo "<div class='side-small text-danger' title='".$locale['figure_1304']." : ".$data['figure_poa_name']." ' alt='".$locale['figure_1304']."'>".trimlink($data['figure_poa_name'],12)."</div>\n";
						echo "</div>\n";
						
						// COLUMN 7 (edit) only for admins
						
						if (iADMIN || iSUPERADMIN) {
						echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";	
													
							global $aidlink;
							$settings = fusion_get_settings();
								// ['cifg_0005'] = "Edit";
								
								echo "<a class='' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_form&amp;action=edit&amp;figure_id=".$data['figure_id']."'><span class='fa fa-pencil-square-o' aria-hidden='true'></span></a>"; 
							echo "</div>\n";
							} else {
								
								// SHOW NOTHING - ITS ONLY FOR ADMINS
						
							}
						
						// COLUMN 8 Usercount
						if (iADMIN || iSUPERADMIN) {	
							
							// nicht zu sehen für admins
							
							} else {
													
									echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
									$count = dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."'");	
							
										echo "<div class='side-small' title='".$locale['CLFP_020']." : ".$count."' alt='".$locale['CLFP_020']." : ".$count."'>".trimlink($count,6)."</div>\n";
									echo "</div>\n";	
							}		
							
						// COLUMN 9 (add/remove)
						echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";	
	
							// MyCollection
							if (iMEMBER) {
										
								// Check if User has it in his Collection
								echo openform("collectionform", "post", FUSION_SELF);
								if (dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'")) {
									echo form_button("remove_figure", "", $data['figure_id'], ["class" => "btn-xs btn-success", "alt" => $locale['userfigure_002'], "icon" => "fa fa-fw fa-check-square-o"]);
								} else {
									echo form_button("add_figure", "", $data['figure_id'], ["class" => "btn-xs btn-default", "alt" => $locale['userfigure_001'], "icon" => "fa fa-fw fa-plus-square-o"]);
								}
								echo closeform();
												
							} else {
								// $locale['mc_0011']= "This feature is only available for registered members. Please Sign up ";
								echo "<span alt='".$locale['userfigure_001']." | ".$locale['mc_0011']."' title='".$locale['userfigure_001']." | ".$locale['mc_0011']."' class='fa fa-eye-slash' aria-hidden='true'></span>";
							}						
						echo "</div>\n";
						
				echo "</div>\n";
				//echo "</div>\n";
				echo "</div>\n";
						
		}
		// PAGE NAV
		echo $info['page_nav'] ? "<div class='text-right'>".$info['page_nav']."</div>" : '';
		closeside();
    }				 
	 }	  
	  
	  echo"</div>";
  
		echo "</div>";
	    echo "</div>";

#############################################################################################################
################## The figure, which most be in the user's possession.#######################################
#############################################################################################################
  
  echo "<div class='panel panel-default'>
    <div class='panel-heading'>
      <h4 class='panel-title'>
        <a data-toggle='collapse' data-parent='#accordion' href='#collapse4'>".$locale['CLFP_025']."</a>
      </h4>
    </div>
    <div id='collapse4' class='panel-collapse collapse'>
      <div class='panel-body'>";
	  
	  	// SEETING FIGURES PER SITE
		$limitmostusercount = 10; 
		
		// Set Empty Arrays
		$info = array("figure_rows" => 0, "page_nav" => false);
		$info['item'] = array();

		// Count all Figures
		//$max_rows3 = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe=1 
		//AND ".groupaccess("figure_visibility")."");
		
		 $max_rows3  = dbcount("              
                        tb.*,             
                        fuf.*
                    FROM ".DB_FIGURE_ITEMS." AS tb              
                    LEFT JOIN ".DB_FIGURE_USERFIGURES." AS fuf ON fuf.figure_userfigures_figure_id=tb.figure_id
                    WHERE ".(multilang_table("FI") ? "tb.figure_language='".LANGUAGE."' 
					AND" : "")." tb.figure_freigabe='1' 						
                  ");	
		
		
		
		// Check Rowstart
		$_GET['rowstart'] = isset($_GET['rowstart']) && isnum($_GET['rowstart']) && $_GET['rowstart'] <= $max_rows3 ? $_GET['rowstart'] : 0;	
	
	if ($max_rows3) {
	  
	  $resultmostusercount = dbquery("
                    SELECT 
                        tb.figure_id, tb.figure_submitter, tb.figure_retailprice, tb.figure_usedprice, tb.figure_freigabe, tb.figure_pubdate, tb.figure_scale, tb.figure_title, tb.figure_manufacturer, tb.figure_brand, tb.figure_datestamp, tb.figure_cat, 
                        tbc.figure_cat_id, tbc.figure_cat_name, 
                        tbu.user_id, tbu.user_name, tbu.user_status, tbu.user_avatar, 
                        tbm.figure_manufacturer_name, 
                        tbb.figure_brand_name, 
                        tbs.figure_scale_id, tbs.figure_scale_name,                             
                        fuf.figure_userfigures_figure_id, fuf.figure_userfigures_user_id,
                        count(fuf.figure_userfigures_user_id) AS users_counter
                    FROM ".DB_FIGURE_ITEMS." AS tb
                    LEFT JOIN ".DB_USERS." AS tbu ON tb.figure_submitter=tbu.user_id
                    LEFT JOIN ".DB_FIGURE_USERFIGURES." AS fuf ON fuf.figure_userfigures_figure_id=tb.figure_id
                    LEFT JOIN ".DB_FIGURE_CATS." AS tbc ON tb.figure_cat=tbc.figure_cat_id
                    LEFT JOIN ".DB_FIGURE_MANUFACTURERS." AS tbm ON tbm.figure_manufacturer_id = tb.figure_manufacturer
                    LEFT JOIN ".DB_FIGURE_BRANDS." AS tbb ON tbb.figure_brand_id = tb.figure_brand
                    LEFT JOIN ".DB_FIGURE_SCALES." AS tbs ON tbs.figure_scale_id = tb.figure_scale
                    WHERE ".(multilang_table("FI") ? "tb.figure_language='".LANGUAGE."' AND" : "")." tb.figure_freigabe='1' 
                    GROUP BY fuf.figure_userfigures_figure_id
                    ORDER BY users_counter DESC 
					LIMIT ".$_GET['rowstart'].",".$limitmostusercount."		
						
                  ");	

				// Pagenav		
			  $info['page_nav'] = $max_rows3 > $limitmostusercount ? makepagenav($_GET['rowstart'], $limitmostusercount, $max_rows3, 3, FUSION_SELF."?") : 0;

				
    if (dbrows($resultmostusercount) == 0) {
		
					openside("");
								
					echo "<div class='row'>\n";
					echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
					//['CLFP_024']= "Not enough data to create the statistics available.";
					echo "<div style='text-align: center;'>".$locale['CLFP_024']."</div>"; 
					echo "</div>\n";
					echo "</div>\n";
			
			closeside();
			
		} else {
		
						
		openside("");				
		 

				/*//// GRID SYSTEM FOR PANEL AS OVERVIEW ///////////////////////////////////////
					echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	image
					echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";	title
					echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";	manufacturer
					echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";	brand
					echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";	scale
					echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";	year
					echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";	rating
					echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";	edit (only for admins)
					echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";	add/remove
				//////////////////////////////////////////////////////////////////////////////*/
								
				echo "<div class='navbar-default text-bold'>";	
				echo "<div class='text-bold'>";				
				echo "<div class='container-fluid'>\n";
				//echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	
		
						// COLUMN 1 (image)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_018']."</div>\n";
						echo "</div>\n";

						// COLUMN 2 (name of figure)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
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
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_005']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 6 (release date)
						echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_006']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 7 (rating)
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_010']."</div>\n";
						echo "</div>\n";
						
						if (iADMIN || iSUPERADMIN) {
						// COLUMN 8 (edit) only for admins
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>Edit</div>\n";
						echo "</div>\n";
						} else {}
						
						
						// COLUMN 9 Usercount (only for Guests and members - admins will see edit button)if (iADMIN || iSUPERADMIN) {
							if (iADMIN || iSUPERADMIN) {
								} else {
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase text-danger'>Count</div>\n";
						echo "</div>\n";
						}
						
						// COLUMN 9 (action)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>Opt.</div>\n";
						echo "</div>\n";
											
				echo "</div>\n";
				//echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				
				echo "<hr>";
		 
		while ($data = dbarray($resultmostusercount)) {	
				
						
				echo "<div class='container-fluid'>\n";
				//echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	
				
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
												
						// COLUMN 1 (image clickable)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='side-small'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>\n<img src='".$imageURL."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' style='border:0px;max-height:40px;max-width:40px'/></a>";
						echo "</div></div>\n";				

						// COLUMN 2 (name of figure)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
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
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='side-small' title='".$locale['CLFP_005']." : ".$data['figure_scale_name']."' alt='".$locale['CLFP_005']." : ".$data['figure_scale_name']."'>".trimlink($data['figure_scale_name'],6)."</div>\n";
						echo "</div>\n";
			
						// COLUMN 6 (release date)
						// No release date or unknown = "no data" / WENN KEIN WERT ZUM DATUM IN DB DANN ZEIGE HINWEIS "NO DATA"
						if ($data['figure_pubdate'] == "") {
							
								echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'><div class='side-small' title='".$locale['CLFP_006']." : ".$locale['CLFP_008']."' alt='".$locale['CLFP_006']." : ".$locale['CLFP_008']."'>".trimlink($locale['CLFP_008'],5)."</div></div>\n";
								} else {
								echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'><div class='side-small' title='".$locale['CLFP_006']." : ".$data['figure_pubdate']."' alt='".$locale['CLFP_006']." : ".$data['figure_pubdate']."'>".trimlink($data['figure_pubdate'],7)."</div></div>\n";
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
   
								$rating = ($drating['count_votes'] > 0 ? str_repeat("<img src='".INFUSIONS.$inf_folder."/images/starsmall.png'>", ceil($drating['sum_rating']/$drating['count_votes'])) : "-");
						
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='side-small' title='".$locale['CLFP_010']."' alt='".$locale['CLFP_010']."'>".$rating."</div>\n";
						echo "</div>\n";
						
						// COLUMN 8 (edit) only for admins
						
						if (iADMIN || iSUPERADMIN) {
						echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";	
													
							global $aidlink;
							$settings = fusion_get_settings();
								// ['cifg_0005'] = "Edit";
								
								echo "<a class='' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_form&amp;action=edit&amp;figure_id=".$data['figure_id']."'><span class='fa fa-pencil-square-o' aria-hidden='true'></span></a>"; 
							echo "</div>\n";
							} else {
								
								// SHOW NOTHING - ITS ONLY FOR ADMINS
						
							}
						
						// COLUMN 8 Usercount
						if (iADMIN || iSUPERADMIN) {	
							
							// nicht zu sehen für admins
							
							} else {
													
									echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
									$count = dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."'");	
							
										echo "<div class='side-small text-danger' title='".$locale['CLFP_020']." : ".$count."' alt='".$locale['CLFP_020']." : ".$count."'>".trimlink($count,6)."</div>\n";
									echo "</div>\n";	
							}		
							
						// COLUMN 9 (add/remove)
						echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";	
	
							// MyCollection
							if (iMEMBER) {
										
								// Check if User has it in his Collection
								echo openform("collectionform", "post", FUSION_SELF);
								if (dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'")) {
									echo form_button("remove_figure", "", $data['figure_id'], ["class" => "btn-xs btn-success", "alt" => $locale['userfigure_002'], "icon" => "fa fa-fw fa-check-square-o"]);
								} else {
									echo form_button("add_figure", "", $data['figure_id'], ["class" => "btn-xs btn-default", "alt" => $locale['userfigure_001'], "icon" => "fa fa-fw fa-plus-square-o"]);
								}
								echo closeform();
												
							} else {
								// $locale['mc_0011']= "This feature is only available for registered members. Please Sign up ";
								echo "<span alt='".$locale['userfigure_001']." | ".$locale['mc_0011']."' title='".$locale['userfigure_001']." | ".$locale['mc_0011']."' class='fa fa-eye-slash' aria-hidden='true'></span>";
							}						
						echo "</div>\n";
						
				echo "</div>\n";
				//echo "</div>\n";
				echo "</div>\n";
						
		}
				// PAGE NAV
		echo $info['page_nav'] ? "<div class='text-right'>".$info['page_nav']."</div>" : '';
		closeside();
    }
	 }  
	  
	  echo"</div>";
  

	    echo "</div>";
	echo "</div>\n";
	
#############################################################################################################
################## OLDEST FIGURE ############################################################################
#############################################################################################################
   
   echo "<div class='panel panel-default'>
		<div class='panel-heading'>
			<h4 class='panel-title'>
				<a data-toggle='collapse' data-parent='#accordion' href='#collapse5'>".$locale['CLFP_028']."</a></h4>
		</div>
    
	<div id='collapse5' class='panel-collapse collapse'>
      <div class='panel-body'>";
	  
	  echo"<span>";
	  
	  	// SEETING FIGURES PER SITE
		$limitoldest = 10; 
		
		// Set Empty Arrays
		$info4 = array("figure_rows" => 0, "page_nav" => false);
		$info4['item'] = array();

		// Count all Figures
		$max_rows4 = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe=1 
		AND ".groupaccess("figure_visibility")."
		AND figure_pubdate != ''
		");
		
		// Check Rowstart
		$_GET['rowstart'] = isset($_GET['rowstart']) && isnum($_GET['rowstart']) && $_GET['rowstart'] <= $max_rows4 ? $_GET['rowstart'] : 0;	
	
	if ($max_rows4) {
	  
	  $resultoldest = dbquery("
                     SELECT
                        tb.figure_id, tb.figure_submitter, tb.figure_retailprice, tb.figure_usedprice, tb.figure_freigabe, tb.figure_pubdate, tb.figure_scale, tb.figure_title, tb.figure_manufacturer, tb.figure_brand, tb.figure_datestamp, tb.figure_cat,
                        tbc.figure_cat_id, tbc.figure_cat_name,
                        tbu.user_id, tbu.user_name, tbu.user_status, tbu.user_avatar,
                        tbm.figure_manufacturer_name,
                        tbb.figure_brand_name,                     
                        tbs.figure_scale_id, tbs.figure_scale_name,                     
                        fuf.figure_userfigures_figure_id, fuf.figure_userfigures_user_id
                     FROM ".DB_FIGURE_ITEMS." AS tb
                     LEFT JOIN ".DB_USERS." AS tbu ON tb.figure_submitter=tbu.user_id
                     LEFT JOIN ".DB_FIGURE_USERFIGURES." AS fuf ON fuf.figure_userfigures_figure_id=tb.figure_id
                     LEFT JOIN ".DB_FIGURE_CATS." AS tbc ON tb.figure_cat=tbc.figure_cat_id
                     LEFT JOIN ".DB_FIGURE_MANUFACTURERS." AS tbm ON tbm.figure_manufacturer_id = tb.figure_manufacturer
                     LEFT JOIN ".DB_FIGURE_BRANDS." AS tbb ON tbb.figure_brand_id = tb.figure_brand
                     LEFT JOIN ".DB_FIGURE_SCALES." AS tbs ON tbs.figure_scale_id = tb.figure_scale
                     WHERE ".(multilang_table("FI") ? "tb.figure_language='".LANGUAGE."' 
							AND" : "")." tb.figure_freigabe='1'
							AND figure_pubdate != ''	
							GROUP BY tb.figure_id
							ORDER BY tb.figure_pubdate ASC
							LIMIT ".$_GET['rowstart'].",".$limitoldest."		
						
                  ");	

				// Pagenav		
			  $info4['page_nav'] = $max_rows4 > $limitoldest ? makepagenav($_GET['rowstart'], $limitoldest, $max_rows4, 3, FUSION_SELF."?") : 0;
	  
	// WENN DATEN UNGLEICH = 0 DANN DARSTELLUNG DER DATEN
							
		 if (dbrows($resultoldest) == 0) {
			
			openside("");
			
					echo "<div class='row'>\n";
					echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
					//['CLFP_024']= "Not enough data to create the statistics available.";
					echo "<div style='text-align: center;'>".$locale['CLFP_024']."</div>"; 
					echo "</div>\n";
					echo "</div>\n";
			
			closeside();
			
		} else {
			
			openside("");				  
			 			 
				/*//// GRID SYSTEM FOR PANEL AS OVERVIEW ///////////////////////////////////////
					echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";		image
					echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";		title
					echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";		manufacturer
					echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";	brand
					echo "<div class='col-lg-3 col-md-4 hidden-sm hidden-xs'>\n";	year											
					echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";		edit (only for admins)
					echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";		add/remove
				//////////////////////////////////////////////////////////////////////////////*/
		
				echo "<div class='navbar-default text-bold'>";	
				echo "<div class='text-bold'>";				
				echo "<div class='container-fluid'>\n";
				//echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	
				
		
						// COLUMN 1 (image)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_018']."</div>\n";
						echo "</div>\n";

						// COLUMN 2 (name of figure)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
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
						
						// COLUMN 5 (rating)
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_010']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 6 (release date)
						echo "<div class='col-lg-2 col-md-4 hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase text-danger'>".$locale['CLFP_006']."</div>\n";
						echo "</div>\n";
						
						
					if (iADMIN || iSUPERADMIN) {
						// COLUMN 7 (edit) only for admins
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>Edit</div>\n";
						echo "</div>\n";
					} else {
						
					}
												
						// COLUMN 8 Usercount (only for Guests and members - admins will see edit button)if (iADMIN || iSUPERADMIN) {
					if (iADMIN || iSUPERADMIN) {
					} else {
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>Count</div>\n";
						echo "</div>\n";
					}
						
						// COLUMN 8 (action)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='text-smaller text-uppercase'>Opt.</div>\n";
						echo "</div>\n";
											
				echo "</div>\n";
				//echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				
				echo "<hr>";
		 
	while($data = dbarray($resultoldest)){
									
				echo "<div class='container-fluid'>\n";
				//echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	
				
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
												
						// COLUMN 1 (image clickable)
						echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
							echo "<div class='side-small'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>\n<img src='".$imageURL."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' style='border:0px;max-height:40px;max-width:40px'/></a>";
						echo "</div></div>\n";				

						// COLUMN 2 (name of figure)
						echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
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
			
						// COLUMN 5 (rating)
								$drating = dbarray(dbquery("
								   SELECT 
										SUM(rating_vote) sum_rating, 
										COUNT(rating_item_id) count_votes 
										FROM ".DB_RATINGS." 
										WHERE rating_type='FI' 
										AND  rating_item_id='".$data['figure_id']."'
									")); 
   
								$rating = ($drating['count_votes'] > 0 ? str_repeat("<img src='".INFUSIONS.$inf_folder."/images/starsmall.png'>", ceil($drating['sum_rating']/$drating['count_votes'])) : "-");
						
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='side-small' title='".$locale['CLFP_010']."' alt='".$locale['CLFP_010']."'>".$rating."</div>\n";
						echo "</div>\n";
						
						
						// COLUMN 6 (release date)
						// No release date or unknown = "no data" / WENN KEIN WERT ZUM DATUM IN DB DANN ZEIGE HINWEIS "NO DATA"
						if ($data['figure_pubdate'] == "") {
							
								echo "<div class='col-lg-2 col-md-4 hidden-sm hidden-xs text-danger'><div class='side-small' title='".$locale['CLFP_006']." : ".$locale['CLFP_008']."' alt='".$locale['CLFP_006']." : ".$locale['CLFP_008']."'>".trimlink($locale['CLFP_008'],12)."</div></div>\n";
								} else {
								echo "<div class='col-lg-2 col-md-4 hidden-sm hidden-xs text-danger'><div class='side-small' title='".$locale['CLFP_006']." : ".$data['figure_pubdate']."' alt='".$locale['CLFP_006']." : ".$data['figure_pubdate']."'>".trimlink($data['figure_pubdate'],12)."</div></div>\n";
								} 								
						
						// COLUMN 7 (edit) only for admins
						
						if (iADMIN || iSUPERADMIN) {
						echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";	
													
							global $aidlink;
							$settings = fusion_get_settings();
								// ['cifg_0005'] = "Edit";
								
								echo "<a class='' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_form&amp;action=edit&amp;figure_id=".$data['figure_id']."'><span class='fa fa-pencil-square-o' aria-hidden='true'></span></a>"; 
							echo "</div>\n";
							} else {
								
								// SHOW NOTHING - ITS ONLY FOR ADMINS
						
							}
						
						// COLUMN 8 Usercount
						if (iADMIN || iSUPERADMIN) {	
							
							// nicht zu sehen für admins
							
							} else {
													
									echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
									$count = dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."'");	
							
										echo "<div class='side-small' title='".$locale['CLFP_020']." : ".$count."' alt='".$locale['CLFP_020']." : ".$count."'>".trimlink($count,6)."</div>\n";
									echo "</div>\n";	
							}		
							
						// COLUMN 8 (add/remove)
						echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";	
	
							// MyCollection
							if (iMEMBER) {
										
								// Check if User has it in his Collection
								echo openform("collectionform", "post", FUSION_SELF);
								if (dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'")) {
									echo form_button("remove_figure", "", $data['figure_id'], ["class" => "btn-xs btn-success", "alt" => $locale['userfigure_002'], "icon" => "fa fa-fw fa-check-square-o"]);
								} else {
									echo form_button("add_figure", "", $data['figure_id'], ["class" => "btn-xs btn-default", "alt" => $locale['userfigure_001'], "icon" => "fa fa-fw fa-plus-square-o"]);
								}
								echo closeform();
												
							} else {
								// $locale['mc_0011']= "This feature is only available for registered members. Please Sign up ";
								echo "<span alt='".$locale['userfigure_001']." | ".$locale['mc_0011']."' title='".$locale['userfigure_001']." | ".$locale['mc_0011']."' class='fa fa-eye-slash' aria-hidden='true'></span>";
							}						
						echo "</div>\n";
				
				echo "</div>\n";
				//echo "</div>\n";
				echo "</div>\n";				
	}
					// PAGE NAV
		echo $info4['page_nav'] ? "<div class='text-right'>".$info4['page_nav']."</div>" : '';
 closeside();
 }  
	 }   
	  
	  echo"</span>";
	  echo "</div>";
				
		echo "</div>";
	 echo "</div>";

 ////////////////////////////////////////////////////  
   
   
	echo "</div>\n";	





/* TEMPLE WEITERES PANEL
  echo "<div class='panel panel-default'>
			<div class='panel-heading'>
				<h4 class='panel-title'>
					<a data-toggle='collapse' data-parent='#accordion' href='#collapse6'>Collapsible Group 6</a></h4>
			</div>
    
			<div id='collapse6' class='panel-collapse collapse'>
      
	  <div class='panel-body'>";  
	  echo"<span> HIER CODE  HIER CODE HIER CODE </span>";
	  echo "</div>";
  
			echo "</div>";
	    echo "</div>";
	echo "</div>\n";	
*/







	
	


// Close Site
closetable();

// Important File
require_once THEMES."templates/footer.php";


