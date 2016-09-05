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
					

// ################################################################################################ 				

	// Get data for manufacturer Info
	$result = dbquery("
		SELECT	f.*, fm.*					
			FROM ".DB_FIGURE_MANUFACTURERS." AS fm 
			LEFT JOIN ".DB_FIGURE_ITEMS." AS f ON fm.figure_manufacturer_id=f.figure_manufacturer
			WHERE f.figure_freigabe='1' AND figure_cat='".intval($_GET['figure_cat_id'])."' AND figure_manufacturer='".intval($_GET['figure_manufacturer'])."' 
			AND ".groupaccess("figure_visibility")."
			LIMIT 0,1				
	");	

	while ($data = dbarray($result)) {				
				
				echo "	<div class='panel-group'>
						<div class='panel panel-default'>
							<div class='panel-heading'>
								
								<a data-toggle='collapse' href='#collapse1'>".$locale['figm_0046']."</a>";
								
							if (iADMIN || iSUPERADMIN) {
								
								global $aidlink;					
								echo " <a class='fa fa-cog pull-right'  href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_manufacturers&amp;action=edit&amp;manufacturer_id=".intval($_GET['figure_manufacturer'])."'></a>&nbsp;";
							}
								
								
							echo "</div>
						<div id='collapse1' class='panel-collapse collapse'>
						<ul class='list-group'>";	  
   
				// ################################################      
				
				echo "<li class='list-group-item'>
						<div class='row'>
							<div class='col-xs-6 col-sm-6 col-md-6 col-lg-6'>
								<span><strong>Name:</strong> ".$data['figure_manufacturer_name']." </span>
							</div>
							
							<div class='col-xs-6 col-sm-6 col-md-6 col-lg-6'>
								<div class='pull-right'>";
									if ($data['figure_manufacturer_image'])  {	
									echo "<img src='".figures_getImagePath("manufacturers", "thumb", $data['figure_manufacturer_id'])."' style='max-width: 70px;' />";
									}
							echo "</div>
						</div>
					</li>";
				
				// ################################################        
					
		if ( $data['figure_manufacturer_url'] || $data['figure_manufacturer_email'] || 	$data['figure_manufacturer_facebook'] || 
		$data['figure_manufacturer_twitter'] || $data['figure_manufacturer_youtube'] || $data['figure_manufacturer_pinterest'] || 
		$data['figure_manufacturer_instagram'] || 	$data['figure_manufacturer_googleplus']) {
					
					echo "<li class='list-group-item'>"; 			
					echo "<span class='text-bold'>Online: </span>";
						
						if ($data['figure_manufacturer_url'])  {				
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_url']."'><i class='fa fa-globe' aria-hidden='true'> ".$locale['figm_0030']."</i></a>\n";
						} else { } 
				// -----------------------------------------------		
						 if ($data['figure_manufacturer_email'])  {					
								echo "&nbsp;<a class='' href='".$data['figure_manufacturer_email']."'><i class='fa fa-envelope' aria-hidden='true'> ".$locale['figm_0031']."</i></a>\n";
						} else { } 
				// --------------------------------------- --------	
						if ($data['figure_manufacturer_facebook'])  {
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_facebook']."'><i class='fa fa-facebook-official' aria-hidden='true'> ".$locale['figm_0032']."</i></a>\n";
						} else { } 
				// -----------------------------------------------	
						if ($data['figure_manufacturer_twitter'])  {
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_twitter']."'><i class='fa fa-twitter' aria-hidden='true'> ".$locale['figm_0033']."</i></a>\n";
						} else { } 
				// -----------------------------------------------	
						if ($data['figure_manufacturer_youtube'])  {
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_youtube']."'><i class='fa fa-youtube' aria-hidden='true'> ".$locale['figm_0034']."</i></a>\n";
						} else { }  
				// -----------------------------------------------	
						if ($data['figure_manufacturer_pinterest'])  {
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_pinterest']."'><i class='fa fa-pinterest' aria-hidden='true'> ".$locale['figm_0036']."</i></a>\n";
						} else { }  
				// -----------------------------------------------			
						if ($data['figure_manufacturer_instagram'])  {
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_instagram']."'><i class='fa fa-instagram' aria-hidden='true'> ".$locale['figm_0037']."</i></a>\n";
						} else { }  
				// -----------------------------------------------	
						if ($data['figure_manufacturer_googleplus'])  {
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_googleplus']."'><i class='fa fa-google-plus' aria-hidden='true'> ".$locale['figm_0038']."</i></a>\n";				
						} else { }  						
				} else { }	
					echo "</li>";
				
				// ################################################ 
					if ($data['figure_manufacturer_address'])  {
							echo "<li class='list-group-item'>";
								echo "<span class='text-bold'>".$locale['figm_0035']." </span>";
								$manufacturer_address = strip_tags(parse_textarea($data['figure_manufacturer_address']));
							echo $manufacturer_address;
							echo "</li>";
					} else { }   
				// -----------------------------------------------	
				 
					if ($data['figure_manufacturer_description'])  {
							echo "<li class='list-group-item'>";
								echo "<span class='text-bold'>".$locale['figm_0045']." </span>";
								$manufacturer_description = nl2br(parse_textarea($data['figure_manufacturer_description']));
								echo $manufacturer_description;
							echo "</li>";
					} else { } 
				// -----------------------------------------------	
				
		// ADMIN INFOS MANUFACTURERS
								
				if (iADMIN || iSUPERADMIN) {
											
						if ($data['figure_manufacturer_info_admin'])  {
							echo "<li class='list-group-item'>";
								echo "<span class='text-bold'>".$locale['figm_0042'].": </span>";
								$manufacturer_admin_info = nl2br(parse_textarea($data['figure_manufacturer_info_admin']));
								echo $manufacturer_admin_info;
							echo "</li>";
						} else { } 				
				}
				
					/*	$locale['figm_0039'] = "Affiliate Program";
						$locale['figm_0040'] = "Affiliate URL";
						$locale['figm_0041'] = "Affiliate Code";
						'manufacturer_affiliate_program' => $data['figure_manufacturer_affiliate_program'],
						'manufacturer_affiliate_url' => $data['figure_manufacturer_affiliate_url'],
						'manufacturer_affiliate_code' => $data['figure_manufacturer_affiliate_code'],	*/				
				
      echo "</ul>";
      //echo "<div class='panel-footer'></div>";
    echo "</div>
  </div>
</div>";

	}	
// ######################################################################################################		
															
					// Beginn Figure Datas
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
					
		echo "</center>\n";
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
										
					// blaue Version der Box
					// ['figure_1710'] = "There are no Figures in this Category.";
					echo "<div class='alert alert-info m-b-20 submission-guidelines'><br /><center>".$locale['figure_1710']."<br>";
						if (iMEMBER && $fil_settings['figure_submit']) {
							//['figure_521'] = "Submit Figure";
								echo "<p><a href='submit.php?stype=f'>".$locale['figure_521']."</a></p>";
								echo "</div>\n";
						} else {
								echo "</div>\n";
								}
					
					echo "</aside>";	
					
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
					
					
// ################################################################################################ 				

	// Get data for manufacturer Info
	$result = dbquery("
		SELECT	f.*, fm.*					
			FROM ".DB_FIGURE_MANUFACTURERS." AS fm 
			LEFT JOIN ".DB_FIGURE_ITEMS." AS f ON fm.figure_manufacturer_id=f.figure_manufacturer
			WHERE f.figure_freigabe='1' AND figure_cat='".intval($_GET['figure_cat_id'])."' AND figure_manufacturer='".intval($_GET['figure_manufacturer'])."' 
			AND ".groupaccess("figure_visibility")."
			LIMIT 0,1				
	");	

	while ($data = dbarray($result)) {				
				
				echo "	<div class='panel-group'>
						<div class='panel panel-default'>
							<div class='panel-heading'>
								
								<a data-toggle='collapse' href='#collapse1'>".$locale['figm_0046']."</a>";
								
							if (iADMIN || iSUPERADMIN) {
								
								global $aidlink;					
								echo " <a class='fa fa-cog pull-right'  href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_manufacturers&amp;action=edit&amp;manufacturer_id=".intval($_GET['figure_manufacturer'])."'></a>&nbsp;";
							}
								
								
							echo "</div>
						<div id='collapse1' class='panel-collapse collapse'>
						<ul class='list-group'>";	  
   
				// ################################################      
				
				echo "<li class='list-group-item'>
						<div class='row'>
							<div class='col-xs-6 col-sm-6 col-md-6 col-lg-6'>
								<span><strong>Name:</strong> ".$data['figure_manufacturer_name']." </span>
							</div>
							
							<div class='col-xs-6 col-sm-6 col-md-6 col-lg-6'>
								<div class='pull-right'>";
									if ($data['figure_manufacturer_image'])  {	
									echo "<img src='".figures_getImagePath("manufacturers", "thumb", $data['figure_manufacturer_id'])."' style='max-width: 50px;' />";
									}
							echo "</div>
						</div>
					</li>";
				
				// ################################################        
					
					echo "<li class='list-group-item'>"; 			
					echo "<span class='text-bold'>Online: </span>";
						
						if ($data['figure_manufacturer_url'])  {				
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_url']."'><i class='fa fa-globe' aria-hidden='true'> ".$locale['figm_0030']."</i></a>\n";
						} else { } 
				// -----------------------------------------------		
						 if ($data['figure_manufacturer_email'])  {					
								echo "&nbsp;<a class='' href='".$data['figure_manufacturer_email']."'><i class='fa fa-envelope' aria-hidden='true'> ".$locale['figm_0031']."</i></a>\n";
						} else { } 
				// --------------------------------------- --------	
						if ($data['figure_manufacturer_facebook'])  {
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_facebook']."'><i class='fa fa-facebook-official' aria-hidden='true'> ".$locale['figm_0032']."</i></a>\n";
						} else { } 
				// -----------------------------------------------	
						if ($data['figure_manufacturer_twitter'])  {
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_twitter']."'><i class='fa fa-twitter' aria-hidden='true'> ".$locale['figm_0033']."</i></a>\n";
						} else { } 
				// -----------------------------------------------	
						if ($data['figure_manufacturer_youtube'])  {
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_youtube']."'><i class='fa fa-youtube' aria-hidden='true'> ".$locale['figm_0034']."</i></a>\n";
						} else { }  
				// -----------------------------------------------	
						if ($data['figure_manufacturer_pinterest'])  {
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_pinterest']."'><i class='fa fa-pinterest' aria-hidden='true'> ".$locale['figm_0036']."</i></a>\n";
						} else { }  
				// -----------------------------------------------			
						if ($data['figure_manufacturer_instagram'])  {
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_instagram']."'><i class='fa fa-instagram' aria-hidden='true'> ".$locale['figm_0037']."</i></a>\n";
						} else { }  
				// -----------------------------------------------	
						if ($data['figure_manufacturer_googleplus'])  {
									echo "&nbsp;<a class='' href='".$data['figure_manufacturer_googleplus']."'><i class='fa fa-google-plus' aria-hidden='true'> ".$locale['figm_0038']."</i></a>\n";				
						} else { }  						
					
					echo "</li>";
				
				// ################################################ 
					if ($data['figure_manufacturer_address'])  {
							echo "<li class='list-group-item'>";
								echo "<span class='text-bold'>".$locale['figm_0035']." </span>";
								$manufacturer_address = strip_tags(parse_textarea($data['figure_manufacturer_address']));
							echo $manufacturer_address;
							echo "</li>";
					} else { }   
				// -----------------------------------------------	
				 
					if ($data['figure_manufacturer_description'])  {
							echo "<li class='list-group-item'>";
								echo "<span class='text-bold'>".$locale['figm_0045']." </span>";
								$manufacturer_description = nl2br(parse_textarea($data['figure_manufacturer_description']));
								echo $manufacturer_description;
							echo "</li>";
					} else { } 
				// -----------------------------------------------	
				
		// ADMIN INFOS MANUFACTURERS
								
				if (iADMIN || iSUPERADMIN) {
											
						if ($data['figure_manufacturer_info_admin'])  {
							
						echo "<li class='list-group-item alert alert-danger'>"; 
								echo "<span class='text-bold'>".$locale['figm_0042'].": </span>";
								$manufacturer_admin_info = nl2br(parse_textarea($data['figure_manufacturer_info_admin']));						
									echo $manufacturer_admin_info;

							echo "</li>";
						} else { } 				
				}
				
					/*	$locale['figm_0039'] = "Affiliate Program";
						$locale['figm_0040'] = "Affiliate URL";
						$locale['figm_0041'] = "Affiliate Code";
						'manufacturer_affiliate_program' => $data['figure_manufacturer_affiliate_program'],
						'manufacturer_affiliate_url' => $data['figure_manufacturer_affiliate_url'],
						'manufacturer_affiliate_code' => $data['figure_manufacturer_affiliate_code'],	*/				
				
      echo "</ul>";
      echo "<div class='panel-footer'></div>";
    echo "</div>
  </div>
</div>";

	}	
// ######################################################################################################	
			
					
					
					
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
				echo "<a href='".$data['figure']['link']."' alt='Figure Title: ".$data['figure']['name']."' title='Figure Title: ".$data['figure']['name']."'>".trimlink($data['figure']['name'],40)."</a>\n";
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
					echo "<center><a href='".$data['figure']['link']."'><img src='".figures_getImagePath("figure", "thumb", $data['figure_id'])."' alt='".trimlink($data['figure_title'],100)."' title='Figure Title: ".trimlink($data['figure_title'],100)."' style='border:0px;max-height:120px;max-width:120px' /><br /></a>";
				echo "</div>\n";
	// ..................... VARIANT				
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					if ($data['figure']['variant']) {
							echo "<span class='small' alt='Variant: ".$data['figure']['variant']."' title='Variant: ".$data['figure']['variant']."'><strong>Variant: </strong>".trimlink($data['figure']['variant'],15)."</span>\n";
					} else {
							echo "<span class='small' alt='Variant: missing data' title='Variant: missing data'><strong>Variant: </strong> no data </span>";
						}
				echo "</div>\n";

	// ..................... SUBMITTER					
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					echo "<span class='small' alt='Submitted by:' title='Submitted by:'><strong>Submitted by: </strong>".profile_link($data['figure']['userid'], trimlink($data['figure']['username'],15), $data['figure']['userstatus'])."<br/></span>\n";
				echo "</div>\n";
				
// ZEILE 2				
	// ..................... MANUFACTURER								
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					echo "<span class='small' alt='Manufacturer: ".$data['figure']['manufacturer']."' title='Manufacturer: ".$data['figure']['manufacturer']."'><strong>Manufacturer: </strong>".trimlink($data['figure']['manufacturer'],15)."<br/></span>\n";
				echo "</div>\n";

	// ..................... SUBMITTED ON				
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					echo "<span class='small' alt='Submitted on:' title='Submitted on:'><strong>Submitted on: </strong>".showdate("shortdate", $data['figure_datestamp'])."</span>\n";
				echo "</div>\n";
// ZEILE 3				
	// ..................... BRAND				
				echo "<div class='col-lg-4 col-md-6 col-sm-12 col-xs-12'>\n";
					echo "<span class='small' alt='Brand: ".$data['figure']['brand']."' title='Brand: ".$data['figure']['brand']."'><strong>Brand: </strong>".trimlink($data['figure']['brand'],15)."</span>\n";
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
						echo "<span class='small' alt='Serie: ".$data['figure']['series']."' title='Serie: ".$data['figure']['series']."'><strong>Series: </strong>".trimlink($data['figure']['series'],15)."</span>\n";
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
			
					// blaue Version der Box
					// ['figure_1710'] = "There are no Figures in this Category.";
					echo "<div class='alert alert-info m-b-20 submission-guidelines'><br /><center>".$locale['figure_1710']."<br>";
					
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
	
}						

