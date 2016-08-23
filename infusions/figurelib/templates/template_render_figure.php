<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: template/template_render_figure.php based on template/weblinks.php
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
// FIGURE OVERVIEW PER CATEGORY
// ******************************************************************************************
	
	// GET SETTING FOR SHOW AS GALLERY OR TABLE
	$fil_settings = get_settings("figurelib"); 
	if ($fil_settings['figure_display']) {	
		
	// GALLERY VIEW 

	/******************************************************************************************		
			|------------------|
			|                  |
			|    - - - - - -   |
			|    |         |   |
			|    |  IMAGE  |   |
			|    |         |   |  
			|    |         |   |
			|    - - - - - -   |
			|                  |
			|	MANUFACTURER   |
			|      TITLE       |
			|      DATE        |
			|      VIEW        |
			|    USER COUNT    |
			|    COMMENTS      |
			|     RATING	   |		          
			|------------------|
	******************************************************************************************/	
		
		if (!function_exists('render_figure')) {
			function render_figure($info) {
						global $locale;
						echo render_breadcrumbs();
				openside('');							
				if ($info['figure_rows'] != 0) {						
							$counter = 0;
							
							// DIESER WERT IST WICHTIG WENN MAN MEHRERE FIGUREN NBENEINANDER HABEN WILL
							// wert aus settings holen
							$fil_settings = get_settings("figurelib");
							
							// Anzahl der spalten ( 4 max ratsam )						
							$columns = $fil_settings['figure_per_line'];
							
							echo "<div class='row m-0'>\n";
							
					if (!empty($info['item'])) {
								
						foreach($info['item'] as $figure_id => $data) {
								
								if ($counter != 0 && ($counter%$columns == 0)) {
										echo "</div>\n<div class='row m-0'>\n";
								}			
						
							echo "<div class='col-xs-6 col-sm-3 col-md-3 col-lg-3 p-t-20'>\n";
							echo "<div class='media'>\n";		
														
							// BOOTSTRAP GIRD BEGIN
							echo "<div class='row'>";
							
							
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
							echo "<center><a href='".$data['figure']['link']."'><img src='".$imageURL."' alt='".trimlink($data['figure_title'],100)."' title='".trimlink($data['figure_title'],50)."' style='border:0px;max-height:100px;max-width:100px' /><br />";						

							// ......................MANUFACTURER & FIGURE TITLE	
									echo "<strong>".$locale['figure_453']."".trimlink($data['figure']['manufacturer'],15)."".$locale['figure_454']."</strong><br />\n";	
									echo "<strong>".trimlink($data['figure']['name'],10)."</strong></a><br />\n";
				
							// ......................DATE	
									echo "<span class='small'><i class='glyphicon glyphicon-time' alt='Added on::' title='Added on'></i>  ".showdate("shortdate", $data['figure_datestamp'])."</span><br>\n";

							// ......................VIEWS	
									echo "<span class='small'><i class='glyphicon glyphicon-eye-open' alt='Views:' title='Views'></i>  ".$data['figure']['views']."</span><br>\n";

							// ......................USERFIGURES COUNT						
									$count = dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."'");	
									echo "<span class='small'><i class='glyphicon glyphicon-user' alt='Usercount:' title='Usercount'></i> ".$count."</span><br>";						

							// ......................COMMENTS	
									$comments = dbcount("(comment_id)", DB_COMMENTS, "comment_type='FI' AND comment_item_id='".$data['figure_id']."'");
									echo "<span class='small'><i class='glyphicon glyphicon-comment' alt='Comments:' title='Comments'></i>  ".$comments."<br/></span>\n";

							// ......................RATING
									$drating = dbarray(dbquery("
										SELECT 
											SUM(rating_vote) sum_rating, 
												COUNT(rating_item_id) count_votes 
												FROM ".DB_RATINGS." 
												WHERE rating_type='FI' 
												AND  rating_item_id='".$data['figure_id']."'
										")); 
									$rating = ($drating['count_votes'] > 0 ? str_repeat("<img src='".INFUSIONS."figurelib/images/starsmall.png'>",ceil($drating['sum_rating']/$drating['count_votes'])) : "-");
									echo "<span class='small'><i class='glyphicon glyphicon-star'></i><i class='glyphicon glyphicon-star'></i><i class='glyphicon glyphicon-star-empty' alt='Rating:' title='Rating'></i>  ".$rating."</span>\n";
							
								echo "</div>\n";
								echo "</div>\n";
								echo "</div>\n";

									$counter++;
						}
					}
							
								echo "</div>\n";
		
				} else {
				
									// ['figc_0012'] = "No figure categories defined";
							
									// SETTINGS HOLEN
									$fil_settings = get_settings("figurelib");			
																			
									// blaue Version der Box
									echo "<div class='alert alert-info m-b-20 submission-guidelines'><br /><center>\n".$locale['figc_0012']."<br>";
										if (iMEMBER && $fil_settings['figure_submit']) {
											//['figure_521'] = "Submit Figure";
												echo "<p><a href='submit.php?stype=f'>".$locale['figure_521']."</a></p>";
												echo "</div></center>\n";
										} else {
												echo "</div></center>\n";
										}
									
							
				}
						//echo "<hr>\n";
						echo $info['page_nav'] ? "<div class='text-right'>".$info['page_nav']."</div>" : '';
						echo "<hr>\n";
						if (iADMIN || iSUPERADMIN) {		
							global $aidlink;				
							
							echo "<div class='navbar-default'>";
							echo "<div class='container-fluid'>\n";
							echo "<div class='table-responsive'>\n";
							echo "<div class='row'>\n";					

													// ['CLFP_016']." = "Admin"
													echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
														echo "<div align='center'><a href='".INFUSIONS.'figurelib/admin.php'.$aidlink."'>".$locale['CLFP_016']."</a>				  </div></div>\n";
							echo "</div>\n";
							echo "</div>\n";
							echo "</div>\n";
							echo "</div>\n";
							
							echo "<hr>\n";
						}
				closeside();	
			}
		}	
	/******************************************************************************************/	
			
} else {	

	/******************************************************************************************		
			|-------------------------------------------------------|
			| ---------	  VARIANTE            POSTED BY             |
			| |       |	  MANUFACTURER        POST DATE             |
			| |       |   BRAND               VIEWS (clickcount)    |
			| |       |   SERIE               USER HABEN DIESE FIGUR|		
			| |       |   SCALE               COMMENTS              |
			| ---------   YEAR	              RATING                |                 
			--------------------------------------------------------|
	******************************************************************************************/			
			
		if (!function_exists('render_figure')) {
			function render_figure($info) {
						global $locale;
						echo render_breadcrumbs();
						
				openside('');
						
				if ($info['figure_rows'] != 0) {
							$counter = 0;
							$columns = 1;
							echo "<div class='row m-0'>\n";
					if (!empty($info['item'])) {
								
						foreach($info['item'] as $figure_id => $data) {
										
										if ($counter != 0 && ($counter%$columns == 0)) {
												echo "</div>\n<div class='row m-0'>\n";
										}
															
							// BOOTSTRAP GIRD BEGIN						
									echo "<div class='panel panel-default'>\n";

							//############################				
							//	   PANELÜBERSCHRIFT     //
							//############################			
									// ......................FIGURE TITLE		
									echo "<div class='panel-heading'>\n";
									echo "<a href='".$data['figure']['link']."'>".$data['figure']['name']."</a>\n";
									echo "</div>\n";
							//############################				
							//	   PANEL                //
							//############################
													


								echo "<div class='row'>\n";
								
// ----------------------------<!--- Open Main---> Image	
								
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
								
								// Image
								echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'><center><a href='".$data['figure']['link']."'><img src='".$imageURL."' alt='".trimlink($data['figure_title'],100)."' title='".trimlink($data['figure_title'],50)."' style='border:0px;max-height:120px;max-width:120px' /></a></center></div>\n";
								
								
								echo"<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>";

// --------------------------<!--- First Row Right Side--->							

							echo "<div class='row'>\n";
								
								// First Col VARIANT
								echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>\n";
								
								if ($data['figure']['variant']) {
											echo "<span class='small'><strong>Variant: </strong>".trimlink($data['figure']['variant'],10)."</span>\n";
									} else {
											echo "<span class='small'><strong>Variant: </strong>... missing data ...</span>";
									}
								
							echo "</div>\n";
								
								// Second Col SUBMITTER	
								echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'><span class='small'><strong>Submitted by: </strong>".profile_link($data['figure']['userid'], $data['figure']['username'], $data['figure']['userstatus'])."<br/></span></div>\n";
							
							echo "</div>\n";

								
// ---------------------------<!--- Second Row Right Side--->							

							echo "<div class='row'>\n";
							
								// First Col MANUFACTURER
								echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'><span class='small'><strong>Manufacturer: </strong>".trimlink($data['figure']['manufacturer'],10)."<br/></span></div>\n";
							
								// Second Col SUBMITTED ON
								echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'><span class='small'><strong>Submitted on: </strong>".showdate("shortdate", $data['figure_datestamp'])."</span></div>\n";
								
							echo "</div>\n";
// ----------------------------------------------------------------------------	
							//<!--- Third Row Right Side--->
							echo "<div class='row'>\n";
							
								// First Col BRAND
								echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'><span class='small'><strong>Brand: </strong>".trimlink($data['figure']['brand'],10)."</span></div>\n";
							
								// Second Col VIEWS	
								echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'><span class='small'><strong>Views: </strong>".$data['figure']['views']."</span></div>\n";
								
							echo "</div>\n";
// ----------------------------------------------------------------------------								
							//<!--- Fourt Row Right Side--->
							echo "<div class='row'>\n";
							
								// First Col SCALE	
								echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'><span class='small'><strong>Scale: </strong>".$data['figure']['scale']."</span></div>\n";
							
								// Second Col. USER COUNT	
								$count = dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."'");	
								echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'><span class='small'><strong>User Count: </strong>".$count."</span></div>\n";
								
							echo "</div>\n";
// ----------------------------------------------------------------------------									
							//<!--- Fifth Row Right Side--->
							echo "<div class='row'>\n";
								
								// First Col
								echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'><span class='small'><strong>Release: </strong>".$data['figure']['year']."</span></div>\n";
								
								// Second Col
								$comments = dbcount("(comment_id)", DB_COMMENTS, "comment_type='FI' AND comment_item_id='".$data['figure_id']."'");
								echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'><span class='small'><strong>Comments: </strong>".$comments."<br/></div>\n";
							
							echo "</div>\n";
// ----------------------------------------------------------------------------									
							//<!--- Sixt Row Right Side--->
							echo "<div class='row'>\n";
								
								// First Col SERIES	
								echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>\n";
								
								if ($data['figure']['series']) {
										echo "<span class='small'><strong>Series: </strong>".trimlink($data['figure']['series'],10)."</span>\n";
									} else {
										echo "<span class='small'><strong>Series: </strong> ... missing data ...</span>";
									}
																
								echo "</div>\n";
								
								// Second Col RATING
								$drating = dbarray(dbquery("
									 SELECT 
										SUM(rating_vote) sum_rating, 
											COUNT(rating_item_id) count_votes 
											FROM ".DB_RATINGS." 
											WHERE rating_type='FI' 
											AND  rating_item_id='".$data['figure_id']."'
										")); 
									$rating = ($drating['count_votes'] > 0 ? str_repeat("<img src='".INFUSIONS."figurelib/images/starsmall.png'>",ceil($drating['sum_rating']/$drating['count_votes'])) : "-");
								echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'><span class='small'><strong>Rating: </strong>".$rating."</span></div>\n";
							
							echo "</div>\n";
// ----------------------------------------------------------------------------									
								//<!--- Close Main-->
								echo "</div></div>\n";
// ----------------------------------------------------------------------------	
		
								echo "</div>\n";
							
									$counter++;
						}
					}
								echo "</div>\n";
				
				} else {
									
							// NO FIGURE CATEGORIES DEFINED
							
							// SETTINGS HOLEN
							$fil_settings = get_settings("figurelib");			
																	
								// // ['figc_0012'] = "No figure categories defined";
								echo "<div class='alert alert-info m-b-20 submission-guidelines'><br /><center>\n".$locale['figc_0012']."<br>";
								
									if (iMEMBER && $fil_settings['figure_submit']) {
											//['figure_521'] = "Submit Figure";
											echo "<p><a href='submit.php?stype=f'>".$locale['figure_521']."</a></p>";
											echo "</div></center>\n";
									} else {
											echo "</div></center>\n";
									}
									
						
				}
							echo $info['page_nav'] ? "<div class='text-right'>".$info['page_nav']."</div>" : '';
							echo "<hr>\n";
								if (iADMIN || iSUPERADMIN) {		
									global $aidlink;				
										
									echo "<div class='navbar-default'>";
									echo "<div class='container-fluid'>\n";
									echo "<div class='table-responsive'>\n";
									echo "<div class='row'>\n";				

															// ['CLFP_016']." = "Admin"
															echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
																echo "<div align='center'><a href='".INFUSIONS.'figurelib/admin.php'.$aidlink."'>".$locale['CLFP_016']."</a>				  </div></div>\n";
									echo "</div>\n";
									echo "</div>\n";
									echo "</div>\n";
									echo "</div>\n";
									
									echo "<hr>\n";
								}
								
			closeside();						
			}
		}											
}						

