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
					global $locale, $fil_settings;
					echo render_breadcrumbs();
					
					// ['cifg_0009'] = "Filter by:";
					//opentable($locale['cifg_0009']);			
					
					echo "<aside class='list-group-item m-b-20'>\n";
					
		if ($info['figure_rows'] != 0) {						
						$counter = 0;
						
						// DIESER WERT IST WICHTIG WENN MEHRERE FIGUREN NEBENEINANDER HABEN WILL
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
		echo "<div class='row'>";

	// ......................IMAGE				
			echo "<center><a href='".$data['figure']['link']."'><img src='".figures_getImagePath("figure", "thumb", $data['figure_id'])."' alt='".trimlink($data['figure_title'],100)."' title='Figure Title: ".trimlink($data['figure_title'],50)."' style='border:0px;max-height:100px;max-width:100px' /><br /></a>";
			
	// .....................FIGURE TITLE	
			echo "<span class='small' alt='Figure Title: ".$data['figure']['name']."' title='Figure Title: ".$data['figure']['name']."'><i class='glyphicon glyphicon-font' alt='Figure Title: ".$data['figure']['name']."' title='Figure Title: ".$data['figure']['name']."'></i><a href='".$data['figure']['link']."' alt='Figure Title: ".$data['figure']['name']."' title='Figure Title: ".$data['figure']['name']."'>  ".trimlink($data['figure']['name'],10)."</span><br></a>\n";
	
	// ......................MANUFACTURER 	
			echo "<span class='small' alt='Manufacturer: ".$data['figure']['manufacturer']."' title='Manufacturer: ".$data['figure']['manufacturer']."'><i class='glyphicon glyphicon-wrench' alt='Manufacturer: ".$data['figure']['manufacturer']."' title='Manufacturer: ".$data['figure']['manufacturer']."'></i>  ".trimlink($data['figure']['manufacturer'],10)."</span><br>\n";
				
	// ......................DATE	
			echo "<span class='small' alt='Release Year: ".$data['figure']['year']."' title='Release Year: ".$data['figure']['year']."'><i class='glyphicon glyphicon-time' alt='Release Year: ".$data['figure']['year']."' title='Release Year: ".$data['figure']['year']."'></i>  ".trimlink($data['figure']['year'],7)."</span><br>\n";

	// ......................VIEWS	
			echo "<span class='small' alt='Views: ".$data['figure']['views']."' title='Views: ".$data['figure']['views']."'><i class='glyphicon glyphicon-eye-open' alt='Views: ".$data['figure']['views']."' title='Views: ".$data['figure']['views']."'></i>  ".$data['figure']['views']."</span><br>\n";

	// ......................USERFIGURES COUNT						
			$count = dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."'");	
			echo "<span class='small' alt='Usercount: ".$count."' title='Usercount: ".$count."'><i class='glyphicon glyphicon-user' alt='Usercount: ".$count."' title='Usercount: ".$count."'></i> ".$count."</span><br>";						

	// ......................COMMENTS	
			$comments = dbcount("(comment_id)", DB_COMMENTS, "comment_type='FI' AND comment_item_id='".$data['figure_id']."'");
			echo "<span class='small' alt='Comments: ".$comments."' title='Comments: ".$comments."'><i class='glyphicon glyphicon-comment' alt='Comments: ".$comments."' title='Comments: ".$comments."'></i>  ".$comments."<br/></span>\n";

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
			echo "<span class='small' alt='Rating' title='Rating'><i class='glyphicon glyphicon-star'></i><i class='glyphicon glyphicon-star'></i><i class='glyphicon glyphicon-star-empty' alt='Rating' title='Rating'></i>  ".$rating."</span>\n";
					
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
				// ......................
								$counter++;
					}
				}
						
				echo "</div>\n";
	
		} else {
			
					// ['figc_0012'] = "No figure categories defined";
					
										
					/*			
					// graue Version der Box
					echo "<div class='well text-center'><br />\n".$locale['figc_0012']."<br />\n";
						if (iMEMBER && $fil_settings['figure_submit']) {
							//['figure_521'] = "Submit Figure";
								echo "<p><a href='submit.php?stype=f'>".$locale['figure_521']."</a></p>";
								echo "</div>\n";
						} else {
								echo "</div>\n";
								}
					*/	
					
					// blaue Version der Box
					// NEW Locale!
					echo "<div class='alert alert-info m-b-20 submission-guidelines'><br /><center>\nThere are no Figures in this Category.<br>";
						if (iMEMBER && $fil_settings['figure_submit']) {
							//['figure_521'] = "Submit Figure";
								echo "<p><a href='submit.php?stype=f'>".$locale['figure_521']."</a></p>";
								echo "</div>\n";
						} else {
								echo "</div>\n";
								}
							
				}
		
			echo $info['page_nav'] ? "<div class='text-right'>".$info['page_nav']."</div>" : '';
		}
	}	
/******************************************************************************************/	
			
} else {	

/******************************************************************************************		
		|-------------------------------------------------------|
		| VARIANTE                     POSTED BY                |
		| MANUFACTURER                 POST DATE                |
		| BRAND                        VIEWS (clickcount)       |
		| SERIE                        USER HABEN DIESE FIGUR   |		
		| SCALE                        COMMENTS                 |
		| YEAR	                       RATING                   |                 
		--------------------------------------------------------|
******************************************************************************************/			
		
	if (!function_exists('render_figure')) {
		function render_figure($info) {
					global $locale, $fil_settings;
					echo render_breadcrumbs();
					
					// ['cifg_0009'] = "Filter by:";
					//opentable($locale['cifg_0009']);
					echo "<aside class='list-group-item m-b-20'>\n";
					
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
				echo "<a href='".$data['figure']['link']."' alt='Figure Title: ".$data['figure']['name']."' title='Figure Title: ".$data['figure']['name']."'>".trimlink($data['figure']['name'],20)."</a>\n";
				echo "</div>\n";
		// ..................................
//############################				
//	   PANEL                //
//############################
		
		
			echo "<div class='container-fluid'>\n";
			echo "<div class='table-responsive'>\n";
			
			
				
// ZEILE 1
	// ..................... BILD			
				echo "<div class='col-lg-4 col-md-12 col-sm-12 col-xs-12'>\n";
										$result2 = dbquery("SELECT	
											figure_images_image_id, 	
											figure_images_figure_id, 	
											figure_images_image, 	
											figure_images_thumb 	
										FROM ".DB_FIGURE_IMAGES."
										WHERE figure_images_figure_id='".$data['figure_id']."' 
										LIMIT 0,1");
					 
								if(dbrows($result2)){						 
									while($data2 = dbarray($result2)){						
										echo "<center><a href='".$data['figure']['link']."' alt='Figure Title: ".$data['figure']['name']."' title='Figure Title: ".$data['figure']['name']."'><img src='".($data2['figure_images_thumb'] ? THUMBS_FIGURES.$data2['figure_images_thumb'] : IMAGES."imagenotfound.jpg")."' alt='Figure Title: ".trimlink($data['figure_title'],100)."' title='Figure Title: ".trimlink($data['figure_title'],50)."' style='border:0px;max-height:120px;max-width:120px' /></a>";						
									}										
								} else { 									
										echo "<center><a href='".$data['figure']['link']."' alt='Figure Title: ".$data['figure']['name']."' title='Figure Title: ".$data['figure']['name']."'><img src='".IMAGES."imagenotfound.jpg' alt='".trimlink($data['figure_title'],100)."' title='Figure Title: ".trimlink($data['figure_title'],50)."' style='border:0px;max-height:120px;max-width:120px' /></a>";
										}
				echo "</div>\n";
	// ..................... VARIANT				
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					if ($data['figure']['variant']) {
							echo "<span class='small' alt='Variant: ".$data['figure']['variant']."' title='Variant: ".$data['figure']['variant']."'><strong>Variant: </strong>".trimlink($data['figure']['variant'],10)."</span>\n";
					} else {
							echo "<span class='small' alt='Variant: missing data' title='Variant: missing data'><strong>Variant: </strong> no data </span>";
						}
				echo "</div>\n";

	// ..................... SUBMITTER					
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					echo "<span class='small' alt='Submitted by:' title='Submitted by:'><strong>Submitted by: </strong>".profile_link($data['figure']['userid'], trimlink($data['figure']['username'],20), $data['figure']['userstatus'])."<br/></span>\n";
				echo "</div>\n";
				
// ZEILE 2				
	// ..................... MANUFACTURER								
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					echo "<span class='small' alt='Manufacturer: ".$data['figure']['manufacturer']."' title='Manufacturer: ".$data['figure']['manufacturer']."'><strong>Manufacturer: </strong>".trimlink($data['figure']['manufacturer'],10)."<br/></span>\n";
				echo "</div>\n";

	// ..................... SUBMITTED ON				
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					//echo "<span class='small'><strong>Submitted on: </strong>".timer($data['figure_datestamp'])." - ".showdate("shortdate", $data['figure_datestamp'])."</span>\n";
					echo "<span class='small' alt='Submitted on:' title='Submitted on:'><strong>Submitted on: </strong>".showdate("shortdate", $data['figure_datestamp'])."</span>\n";
				echo "</div>\n";
// ZEILE 3				
	// ..................... BRAND				
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					echo "<span class='small' alt='Brand: ".$data['figure']['brand']."' title='Brand: ".$data['figure']['brand']."'><strong>Brand: </strong>".trimlink($data['figure']['brand'],10)."</span>\n";
				echo "</div>\n";

	// ..................... VIEWS				
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					echo "<span class='small' alt='Views: ".$data['figure']['views']."' title='Views: ".$data['figure']['views']."'><strong>Views: </strong>".$data['figure']['views']."</span>\n";
				echo "</div>\n";				
				
// ZEILE 4				
	// ..................... SCALE				
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					echo "<span class='small' alt='Scale: ".$data['figure']['scale']."' title='Scale: ".$data['figure']['scale']."'><strong>Scale: </strong>".$data['figure']['scale']."</span>\n";
				echo "</div>\n";

	// ..................... USER COUNT					
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					$count = dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$data['figure_id']."'");	
					echo "<span class='small' alt='Usercount: ".$count."' title='Usercount: ".$count."'><strong>User Count: </strong>".$count."</span>\n";
				echo "</div>\n";				
// ZEILE 5				
	// ..................... RELEASE DATE			
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					echo "<span class='small' alt='Release Year: ".$data['figure']['year']."' title='Release Year: ".$data['figure']['year']."'><strong>Release: </strong>".$data['figure']['year']."</span>\n";
				echo "</div>\n";

	// ..................... COMMENT COUNT					
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					$comments = dbcount("(comment_id)", DB_COMMENTS, "comment_type='FI' AND comment_item_id='".$data['figure_id']."'");
					echo "<span class='small' alt='Comments: ".$comments."' title='Comments: ".$comments."'><strong>Comments: </strong>".$comments."<br/></span>\n";
				echo "</div>\n";								
// ZEILE 6				
	// ..................... SERIES			
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					if ($data['figure']['series']) {
						echo "<span class='small' alt='Serie: ".$data['figure']['series']."' title='Serie: ".$data['figure']['series']."'><strong>Series: </strong>".trimlink($data['figure']['series'],10)."</span>\n";
					} else {
						echo "<span class='small' alt='Serie: missing data' title='Serie: missing data'><strong>Series: </strong> no data </span>";
					}
				echo "</div>\n";

	// ..................... RATING					
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					$drating = dbarray(dbquery("
					 SELECT 
						SUM(rating_vote) sum_rating, 
							COUNT(rating_item_id) count_votes 
							FROM ".DB_RATINGS." 
							WHERE rating_type='FI' 
							AND  rating_item_id='".$data['figure_id']."'
						")); 
					$rating = ($drating['count_votes'] > 0 ? str_repeat("<img src='".INFUSIONS."figurelib/images/starsmall.png'>",ceil($drating['sum_rating']/$drating['count_votes'])) : "-");
					echo "<span class='small'><strong>Rating: </strong>".$rating."</span>\n";
				echo "</div>\n";				
	// ...........................................						
				
				
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				
					$counter++;
				}
			}
echo "</div>\n";
	
		} else {
						
			// ['figc_0012'] = "No figure categories defined";
				
						
					/* graue Version der Box
					echo "<div class='well text-center'><br />\n".$locale['figc_0012']."<br />\n";
							if (iMEMBER && $fil_settings['figure_submit']) {
								//['figure_521'] = "Submit Figure";
								echo "<p><a href='submit.php?stype=f'>".$locale['figure_521']."</a></p>";
								echo "</div>\n";
					} else {
								echo "</div>\n";
					}
					*/			
			
					// blaue Version der Box
					echo "<div class='alert alert-info m-b-20 submission-guidelines'><br /><center>\nThere are no Figures in this Category.<br>";
					
						if (iMEMBER && $fil_settings['figure_submit']) {
								//['figure_521'] = "Submit Figure";
								echo "<p><a href='submit.php?stype=f'>".$locale['figure_521']."</a></p>";
								echo "</div>\n";
						} else {
								echo "</div>\n";
						}
					
			
			}
				echo $info['page_nav'] ? "<div class='text-right'>".$info['page_nav']."</div>" : '';
				echo "</aside";

		}
	}					
						
	
}						

