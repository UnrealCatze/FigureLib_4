<?php
/***************************************************************************
 *   figure_center_panel.php for FIGURELIB                                  *
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
//require_once "../../../maincore.php";
require_once THEMES."templates/header.php";
include INFUSIONS."figurelib/infusion_db.php";
require_once INCLUDES."infusions_include.php";

global $aidlink;
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

//opentable("<div class='text'>Global Stats</div>");
###########################################################################################################################
#################### GESAMTZAHL ALLER FIGUREN + Gesamtzahl Figuren in Userbesitz        ###################################
###########################################################################################################################

			//openside("");			
				echo "<hr>";
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
			//closeside();	
//closetable();
###########################################################################################################################
############################################### LAST FIGURE  ###########################################################
###########################################################################################################################			
	echo "<hr>";	
 
opentable("<div class='text'>Latest Figures</div>");
	  
	  // SEETING FIGURES PER SITE
		$limit = 5; 
		
		// Set Empty Arrays
		$info = array("figure_rows" => 0, "page_nav" => false);
		$info['item'] = array();

		// Count all Figures
		$max_rows = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe=1 AND ".groupaccess("figure_visibility")."");
		
		// Check Rowstart
		$_GET['rowstart'] = isset($_GET['rowstart']) && isnum($_GET['rowstart']) && $_GET['rowstart'] <= $max_rows ? $_GET['rowstart'] : 0;	
	
	if ($max_rows) {
	  
					$result = dbquery("
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
						GROUP BY tb.figure_id
						ORDER BY tb.figure_datestamp DESC
						LIMIT ".$_GET['rowstart'].",".$limit."					
                  ");
				 
				 // Pagenav		
			  $info['page_nav'] = $max_rows > $limit ? makepagenav($_GET['rowstart'], $limit, $max_rows, 3, FUSION_SELF."?") : 0;
			  

	  
	// WENN DATEN UNGLEICH = 0 DANN DARSTELLUNG DER DATEN
							
		 if (dbrows($result) == 0) {
			
			//openside("");
			
					echo "<div class='row'>\n";
					echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
					//['CLFP_024']= "Not enough data to create the statistics available.";
					echo "<div style='text-align: center;'>".$locale['CLFP_024']."</div>"; 
					echo "</div>\n";
					echo "</div>\n";
			
			//closeside();
			
		} else {
			
			//openside("");				  
			 			 
				/*//// GRID SYSTEM FOR PANEL AS OVERVIEW ///////////////////////////////////////
					echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";		image
					echo "<div class='col-lg-3 col-md-3 col-sm-4 col-xs-4'>\n";		title
					echo "<div class='col-lg-3 col-md-3 col-sm-4 col-xs-4'>\n";		manufacturer
					echo "<div class='col-lg-3 hidden-md hidden-sm hidden-xs'>\n";	brand
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
						echo "<div class='col-lg-3 col-md-3 col-sm-4 col-xs-4'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_002']."</div>\n";
						echo "</div>\n";					
						
						// COLUMN 3 (manufacturer)
						echo "<div class='col-lg-3 col-md-3 col-sm-4 col-xs-4'>\n";	
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_003']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 4 (brand)
						echo "<div class='col-lg-3 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_004']."</div>\n";
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
		 
	while($data = dbarray($result)){
									
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
						echo "<div class='col-lg-3 col-md-3 col-sm-4 col-xs-4'>\n";
							echo "<div class='side-small'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."'>".trimlink($data['figure_title'], 20)."</a>";
							echo "</div></div>\n";	
		
						// COLUMN 3 (manufacturer)
						echo "<div class='col-lg-3 col-md-3 col-sm-4 col-xs-4'>\n";	
							echo "<div class='side-small' title='".$locale['CLFP_003']." : ".$data['figure_manufacturer_name']."' alt='".$locale['CLFP_003']." : ".$data['figure_manufacturer_name']."'>".trimlink($data['figure_manufacturer_name'],20)."</div>\n";
						echo "</div>\n";
						
						// COLUMN 4 (brand)
						echo "<div class='col-lg-3 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='side-small' title='".$locale['CLFP_004']." : ".$data['figure_brand_name']."' alt='".$locale['CLFP_004']." : ".$data['figure_brand_name']."'>".trimlink($data['figure_brand_name'],20)."</div>\n";
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
		echo $info['page_nav'] ? "<div class='text-center'>".$info['page_nav']."</div>" : '';
 //closeside();
 
 }  
	}   
	   

  
  



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




