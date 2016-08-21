<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: template/template_render_figure_items.php based on template/weblinks.php
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
require_once "../../maincore.php";
require_once INCLUDES."infusions_include.php";
if (!defined("IN_FUSION")) { die("Access Denied"); }
include INFUSIONS."figurelib/infusion_db.php";
global $userdata;

// ******************************************************************************************			
// FIGURE
// ******************************************************************************************
if (!function_exists('render_figure_items')) {
	function render_figure_items($info) {
		global $locale;
		echo render_breadcrumbs();
	
$result = dbquery(
			"SELECT 
				f.*,
				fc.figure_cat_id, 
				fc.figure_cat_name, 		
				fu.user_id, 
				fu.user_name, 
				fu.user_status, 
				fu.user_avatar, 
				fman.figure_manufacturer_id,
				fman.figure_manufacturer_name, 
				fb.figure_brand_name, 
				fy.figure_year_id, 
				fy.figure_year, 
				fs.figure_scale_id, 
				fs.figure_scale_name, 
				fl.figure_limitation_id, 
				fl.figure_limitation_name,
				fpoa.figure_poa_id,
				fpoa.figure_poa_name,
				fpack.figure_packaging_id,
				fpack.figure_packaging_name,
				fmat.figure_material_id,
				fmat.figure_material_name				
			FROM ".DB_FIGURE_ITEMS." f
			LEFT JOIN ".DB_USERS." fu ON f.figure_submitter=fu.user_id
			INNER JOIN ".DB_FIGURE_CATS." fc ON f.figure_cat=fc.figure_cat_id
			INNER JOIN ".DB_FIGURE_MANUFACTURERS." fman ON fman.figure_manufacturer_id = f.figure_manufacturer
			INNER JOIN ".DB_FIGURE_BRANDS." fb ON fb.figure_brand_id = f.figure_brand
			INNER JOIN ".DB_FIGURE_SCALES." fs ON fs.figure_scale_id = f.figure_scale
			INNER JOIN ".DB_FIGURE_YEARS." fy ON fy.figure_year_id = f.figure_pubdate
			INNER JOIN ".DB_FIGURE_LIMITATIONS." fl ON fl.figure_limitation_id = f.figure_limitation
			INNER JOIN ".DB_FIGURE_POAS." fpoa ON fpoa.figure_poa_id = f.figure_poa
			INNER JOIN ".DB_FIGURE_PACKAGINGS." fpack ON fpack.figure_packaging_id = f.figure_packaging
			INNER JOIN ".DB_FIGURE_MATERIALS." fmat ON fmat.figure_material_id = f.figure_material
			".(multilang_table("FI") ? "WHERE figure_language='".LANGUAGE."' AND" : "WHERE")." f.figure_freigabe='1'  
			AND figure_id='".$_GET['figure_id']."'
			");
			
	if (dbrows($result) != 0) {
		while ($data = dbarray($result)) {

// IMAGES ####################################################################################	

	openside("<div class='well clearfix'><strong></strong>&nbsp;&nbsp;<a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>".trimlink($data['figure_title'], 23)." (".trimlink($data['figure_manufacturer_name'], 23).") [".$data['figure_year']."]</a></div>");

	// Figure ID
	$figure_id = $data['figure_id'];

	// Alle Bilder zählen - (image count)
	$image_count = dbcount("(figure_images_image_id)", DB_FIGURE_IMAGES, "figure_images_figure_id='".$figure_id."'");

	// Insofern Bilder vorhanden sind, Ausgabe beginnen
	if ($image_count) {
		   
			   // Standard Variablen  (standard variables)
			   $indicators = "";
			   $images     = "";
			   $i          = 0;
   
				// Bilder auslesen (get image data)
				$resultimage = dbquery("SELECT * FROM ".DB_FIGURE_IMAGES." WHERE figure_images_figure_id='".$figure_id."'");
		while ($image_data = dbarray($resultimage)) {

				// Indikator einfügen (add indicators)
				$indicators .= "<li data-target='#myCarousel' data-slide-to='".$i."'".($i == 0 ? " class='active'" : "")."></li>\n";
		   
				// Bild auslesen (get image)
				$image_thumb = get_figure_image_path($image_data['figure_images_image'], $image_data['figure_images_thumb']);
			  if (!$image_thumb) {
				 $image_thumb = IMAGES."imagenotfound70.jpg";
			  }
			  
				$image_image = get_figure_image_path($image_data['figure_images_image'], $image_data['figure_images_image']);
			  if (!$image_image) {
				 $image_image = IMAGES."imagenotfound70.jpg";
			  }
				
					$images .= "<div class='item".($i == 0 ? " active" : "")."'>\n";
					$images .= "<center><a href='".$image_image."'  target='_blank' height='100%' width='100%' alt='".$data['figure_title']."'><img src='".$image_thumb."' alt='".$locale['figure_588']."' /></a>\n";
					$images .= "</div>\n";

				// Counter erhöhen (counter +1)
				$i++;
		}
   
			   // Carousel anzeigen (show carousel)
			   echo "<div id='myCarousel' class='carousel slide' data-ride='carousel'>\n";
				  echo "<ol class='carousel-indicators'>\n";
					 echo $indicators;
				  echo "</ol>\n";
				  echo "<div class='carousel-inner' role='listbox'>\n";
					 echo $images;
				  echo "</div>\n";
				  echo "<a class='left carousel-control' href='#myCarousel' role='button' data-slide='prev'>\n";
					 if ($image_count > 1) {		
						echo "<span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span>\n";
					 	} else {
							
						}
	
					 echo "<span class='sr-only'>Previous</span>\n";
				  echo "</a>\n";
				  echo "<a class='right carousel-control' href='#myCarousel' role='button' data-slide='next'>\n";
				  if ($image_count > 1) {
					 echo "<span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span>\n";
					 	} else {
							
						}					 
					 echo "<span class='sr-only'>Next</span>\n";
				  echo "</a>\n";
			   echo "</div>\n";
	   
			// Variablen löschen (delete variables)
				unset($indicators, $images, $i);
	} else {
				
				// Carousel anzeigen (show carousel)
			   echo "<div id='myCarousel' class='carousel slide' data-ride='carousel'>\n";
				  //echo "<ol class='carousel-indicators'>\n";
					 //echo $indicators;
				  //echo "</ol>\n";
				  echo "<div class='carousel-inner' role='listbox'>\n";
					 echo "<center><img src='".INFUSIONS."figurelib/images/default.png' alt='".$locale['CLFP_002']." : ".$data['figure_title']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' style='border:0px;max-height:300px;max-width:300px'/><br><br>
						<div class='alert alert-warning' role='alert'>
						Photo unavailable. Submit as a registered member. <a href='".BASEDIR."register.php'>Register HERE</a></div>";
				  echo "</div>\n";
				  echo "<a class='left carousel-control'  role='button' data-slide='prev'>\n";
					 //echo "<span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span>\n";
					 echo "<span class='sr-only'>Previous</span>\n";
				  echo "</a>\n";
				  echo "<a class='right carousel-control'  role='button' data-slide='next'>\n";
					 //echo "<span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span>\n";
					 echo "<span class='sr-only'>Next</span>\n";
				  echo "</a>\n";
			   echo "</div>\n";
			
					// Variablen löschen (delete variables)
				unset($indicators, $images, $i);	

   }
//closeside();
// ############################################################################################	

// ###########  FIGURE DETAILS   ##############################################################
//openside('');

	echo "<hr>";

	// BEGINN OF BOOTSTRAP GRID
	echo "<table class='table'>";

			
				echo "<div class='container-fluid'>\n";
				echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	
				
				// MODUL 1 BEGIN ##################################################################################################
									
						// VARIANT LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_441']."</strong>\n";
						echo "</div></div></div>\n";									
						
						// VARIANT DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_variant']."</span>\n";		
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// SERIES LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_439']."</strong>\n";
						echo "</div></div></div>\n";
						
						// SERIES DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_series']."</span>\n";
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// MANUFACTURER LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_417']."</strong>\n";
						echo "</div></div></div>\n";									
						
						// MANUFACTURER DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_manufacturer_name']."</span>\n";		
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Release Date LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_419']."</strong>\n";
						echo "</div></div></div>\n";
						
						// Release Date DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_year']."</span>\n";
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Country LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_436']."</strong>\n";
						echo "</div></div></div>\n";									
						
						// Country DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_country']."</span>\n";		
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Artists Date LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_452']."</strong>\n";
						echo "</div></div></div>\n";
						
						// Artists Date DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_artists']."</span>\n";
						echo "</div>\n";

			echo "</div>\n";
			echo "</div>\n";
			echo "</div>\n";
			// MODUL 1 END ########################################################################################################
						
	echo "<hr>";

			// MODUL 2 Begin  #####################################################################################################
			echo "<div class='container-fluid'>\n";
			echo "<div class='table-responsive'>\n";
			echo "<div class='row'>\n";	
						
						// Scale LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_442']."</strong>\n";
						echo "</div></div></div>\n";									
						
						// Scale DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_scale_name']."</span>\n";		
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Weight LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_443']."</strong>\n";
						echo "</div></div></div>\n";
						
						// Weight DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_weight']."</span>\n";
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Height LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_444']."</strong>\n";
						echo "</div></div></div>\n";									
						
						// Height DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";						
						$height = dbquery("SELECT fm.figure_measurements_id, fm.figure_measurements_inch, f.figure_height
						FROM ".DB_FIGURE_MEASUREMENTS." fm
						INNER JOIN ".DB_FIGURE_ITEMS." f ON f.figure_height = fm.figure_measurements_id
						WHERE figure_id='".$data['figure_id']."'");
				   
						if(dbrows($height)){
						   while($dataheight = dbarray($height)){								
									echo "<span class='small'>&nbsp;".$dataheight['figure_measurements_inch']."</span>\n";		
							}
						}
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Width LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_445']."</strong>\n";
						echo "</div></div></div>\n";
						
						// Width DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
						$width = dbquery("SELECT fm.figure_measurements_id, fm.figure_measurements_inch, f.figure_width
						FROM ".DB_FIGURE_MEASUREMENTS." fm
						INNER JOIN ".DB_FIGURE_ITEMS." f ON f.figure_width = fm.figure_measurements_id
						WHERE figure_id='".$data['figure_id']."'");
				   
						if(dbrows($width)){
						   while($datawidth = dbarray($width)){	
									echo "<span class='small'>&nbsp;".$datawidth['figure_measurements_inch']."</span>\n";
							}
						}
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Depth LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_446']."</strong>\n";
						echo "</div></div></div>\n";									
						
						// Depth DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
						$depth = dbquery("SELECT fm.figure_measurements_id, fm.figure_measurements_inch, f.figure_depth
						   FROM ".DB_FIGURE_MEASUREMENTS." fm
						   INNER JOIN ".DB_FIGURE_ITEMS." f ON f.figure_depth = fm.figure_measurements_id
						   WHERE figure_id='".$data['figure_id']."'");
						   
						 if(dbrows($depth)){
							while($datadepth = dbarray($depth)){	
									echo "<span class='small'>&nbsp;".$datadepth['figure_measurements_inch']."</span>\n";	
							}
						}								
						echo "</div>\n";
						
			echo "</div>\n";
			echo "</div>\n";
			echo "</div>\n";
			// MODUL 2 END ########################################################################################################
			
	echo "<hr>";

			// MODUL 3 Begin  #####################################################################################################
			echo "<div class='container-fluid'>\n";
			echo "<div class='table-responsive'>\n";
			echo "<div class='row'>\n";	
						
						// Brand LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_438']."</strong>\n";
						echo "</div></div></div>\n";									
						
						// Brand DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_brand_name']."</span>\n";		
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Material LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_447']."</strong>\n";
						echo "</div></div></div>\n";
						
						// Material DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_material_name']."</span>\n";
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Articulations Points LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_455']."</strong>\n";
						echo "</div></div></div>\n";									
						
						// Articulations Points DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_poa_name']."</span>\n";		
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Packaging LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_448']."</strong>\n";
						echo "</div></div></div>\n";
						
						// Packaging DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_packaging_name']."</span>\n";
						echo "</div>\n";
											
			echo "</div>\n";
			echo "</div>\n";
			echo "</div>\n";
			// MODUL 3 END ########################################################################################################
			
	echo "<hr>";

			// MODUL 4 Begin  #####################################################################################################
			echo "<div class='container-fluid'>\n";
			echo "<div class='table-responsive'>\n";
			echo "<div class='row'>\n";	
						
						// MSRP (Price by Release) LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_449']."</strong>\n";
						echo "</div></div></div>\n";									
						
						// MSRP (Price by Release) DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_retailprice']."</span>\n";		
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Used Price LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_456']."</strong>\n";
						echo "</div></div></div>\n";
						
						// Used Price DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_usedprice']."</span>\n";
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Limited Edition YES/NO LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_450']."</strong>\n";
						echo "</div></div></div>\n";									
						
						// Limited Edition YES/NO DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_limitation_name']."</span>\n";		
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Edition Size LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_451']."</strong>\n";
						echo "</div></div></div>\n";
						
						// Edition Size DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small'>&nbsp;".$data['figure_editionsize']."</span>\n";
						echo "</div>\n";
												
			echo "</div>\n";
			echo "</div>\n";
			echo "</div>\n";
			// MODUL 4 END ########################################################################################################			
				
			
			// MODUL 5 Begin (Video)  #############################################################################################		
		if ($data['figure_videourl']  != "") { 	
	
	echo "<hr>";
		
			echo "<div class='container-fluid'>\n";
			echo "<div class='table-responsive'>\n";
			echo "<div class='row'>\n";	
		
			$videourl = $data['figure_videourl'];
			
				// TEXT
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>Unboxing Video</strong>\n";
						echo "</div></div></div>\n";									
						
				// VIDEO
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
						// dieser code geht nicht 
							echo "<div class='embed-responsive embed-responsive-16by9' ><iframe class='embed-responsive-item' src='https://www.youtube.com/embed/".$videourl."'></iframe></div>";					
						echo "</div>\n";
				
			echo "</div>\n";
			echo "</div>\n";
			echo "</div>\n";
			} else { 			
			}		
			// MODUL 5 End  (Video) ################################################################################################
	
	echo "<hr>";

			// MODUL 6 Begin  #####################################################################################################
			echo "<div class='container-fluid'>\n";
			echo "<div class='table-responsive'>\n";
			echo "<div class='row'>\n";	
						
						// Accessories LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_457']."</strong>\n";
						echo "</div></div></div>\n";									
						
						// Accessories DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";
								echo "<span class='small' style='word-break: break-all; word-wrap: break-word;'>&nbsp;".($data['figure_accessories'] ? $data['figure_accessories'] : "-")."</span>\n";		
						echo "</div>\n";
						
						// ++++++++++++++++
						
						// Discreption  LOCALE
						echo "<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'>\n";
							echo "<div class='navbar-default'><div class='text-smaller text-uppercase'><strong>".$locale['figure_423']."</strong>\n";
						echo "</div></div></div>\n";
						
						// Discreption  DATA
						echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>\n";	
							echo "<span class='small' style='word-break: break-all; word-wrap: break-word;'>&nbsp;".$data['figure_description']."</span>\n";			
						echo "</div>\n";
					
			echo "</div>\n";
			echo "</div>\n";
			echo "</div>\n";
			// MODUL 5 END ########################################################################################################					
				
	// END OF BOOTSTRAP GRID	
	echo "</table>\n";
		
closeside();

// ############################################################################################

// ###### LINK FOR ADMINS TO EDIT THE FIGURE ##################################################
if (iADMIN || iSUPERADMIN) {

	openside("<div class='well clearfix'><strong>ADMIN OPTIONS</strong></div>");

		global $aidlink;
		$settings = fusion_get_settings();
				// ['cifg_0005'] = "Edit";
				echo "<div align='center'>\n";
				echo "<a class='btn btn-sm btn-danger' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_form&amp;action=edit&amp;figure_id=".$_GET['figure_id']."'>".$locale['cifg_0005a']."</a><p>"; 
				echo "</div>";
	closeside();		
}
// ############################################################################################	

// ########  AFFILIATE PANEL  #################################################################
openside("<div class='well clearfix'><strong>AFFILIATES</strong></div>");						
				
	// CSS 
		echo "<style type='text/css'>
		<!--
		table {
		  border-collapse: collapse;
		  border: 1px solid #eeeeee;
		}
		-->
		</style>\n";	
					
			echo "<table style='cellspacing:10px; cellpadding:10px;' class='table' width='100%'>\n";
		
		// FIRST LINE ESHOP (PRIORITY 1)		 
			 if ($data['figure_eshop'] == "") { 
						} else { 
						echo "<tr><td style='text-align:center; vertical-align:middle;' colspan='4'><a href='".$data['figure_eshop']."'</a>".trimlink($data['figure_eshop'],15)."</td></tr>\n"; }	
			 
		// SECOND LINE AFFILIATE (PRIORITY 2)			 
			 if ($data['figure_affiliate_1']  ||  $data['figure_affiliate_2'] ||  $data['figure_affiliate_3'] || $data['figure_affiliate_4'] != "") { 
			 
				 echo "<tr>\n";
				 echo "<colgroup><col width='25%'><col width='25%'><col width='25%'><col width='25%'></colgroup>\n"; 
				 echo "<td style='text-align:center; vertical-align:middle;'>	\n";	 
						if ($data['figure_affiliate_1'] == "") { echo "<strike>".$locale['figure_033']."</strike>";
							} else { echo "<a href='".$data['figure_affiliate_1']."'</a>".trimlink($data['figure_affiliate_1'],15)."</td>\n"; }		 
				 echo "<td align='center'>\n";	 
						if ($data['figure_affiliate_2'] == "") { echo "<strike>".$locale['figure_033']."</strike>";
							} else { echo "<a href='".$data['figure_affiliate_2']."'</a>".trimlink($data['figure_affiliate_2'],15)."</td>\n"; }	
				 echo "<td align='center'>\n";	 
						if ($data['figure_affiliate_3'] == "") { echo "<strike>".$locale['figure_033']."</strike>";
							} else { echo "<a href='".$data['figure_affiliate_3']."'</a>".trimlink($data['figure_affiliate_3'],15)."</td>\n"; }	
				 echo "<td align='center'>\n";	 
						if ($data['figure_affiliate_4'] == "") { echo "<strike>".$locale['figure_033']."</strike>";
							} else { echo "<a href='".$data['figure_affiliate_4']."'</a>".trimlink($data['figure_affiliate_4'],15)."</td>\n"; }	
				 echo "</tr>\n";
			 } else { 
			 }
			 
		// THIRD LINE AFFILIATE (PRIORITY 2)		 
			if ($data['figure_affiliate_5']  ||  $data['figure_affiliate_6'] ||  $data['figure_affiliate_7'] || $data['figure_affiliate_8'] != "") { 
			 
				 echo "<tr>\n";
				 echo "<colgroup><col width='25%'><col width='25%'><col width='25%'><col width='25%'></colgroup>\n"; 
				 echo "<td style='text-align:center; vertical-align:middle;'>	\n";	 
						if ($data['figure_affiliate_5'] == "") { echo "<strike>".$locale['figure_033']."</strike>";
							} else { echo "<a href='".$data['figure_affiliate_5']."'</a>".trimlink($data['figure_affiliate_5'],15)."</td>\n"; }		 
				 echo "<td align='center'>\n";	 
						if ($data['figure_affiliate_6'] == "") { echo "<strike>".$locale['figure_033']."</strike>";
							} else { echo "<a href='".$data['figure_affiliate_6']."'</a>".trimlink($data['figure_affiliate_6'],15)."</td>\n"; }	
				 echo "<td align='center'>\n";	 
						if ($data['figure_affiliate_7'] == "") { echo "<strike>".$locale['figure_033']."</strike>";
							} else { echo "<a href='".$data['figure_affiliate_7']."'</a>".trimlink($data['figure_affiliate_7'],15)."</td>\n"; }	
				 echo "<td align='center'>\n";	 
						if ($data['figure_affiliate_8'] == "") { echo "<strike>".$locale['figure_033']."</strike>";
							} else { echo "<a href='".$data['figure_affiliate_8']."'</a>".trimlink($data['figure_affiliate_8'],15)."</td>\n"; }	
				 echo "</tr>\n";
			
			} else { 
			 }
			
		// FOURTH LINE AMAZON COM CA UK DE	

				echo "<tr>\n";
				echo "<colgroup><col width='25%'><col width='25%'><col width='25%'><col width='25%'></colgroup>\n"; 
						
				echo "<td style='text-align:center; vertical-align:middle;'>	\n";	 
					if ($data['figure_amazon_com'] == "") { echo "<img src='".INFUSIONS."figurelib/images/flags/flag_usa_sw.png"."' alt='".$locale['figure_031a']."' title='".$locale['figure_031a']."'>";
						} else { echo "<a href='".$data['figure_amazon_com']."'><img src='".INFUSIONS."figurelib/images/flags/flag_usa.png"."' alt='".trimlink($data['figure_amazon_com'],50)."' title='".trimlink($data['figure_amazon_com'],100)."'></td>\n"; }		 
			
				echo "<td align='center'>\n";	 
					if ($data['figure_amazon_ca'] == "") { echo "<img src='".INFUSIONS."figurelib/images/flags/flag_canada_sw.png"."' alt='".$locale['figure_032a']."' title='".$locale['figure_032a']."'>";
						} else { echo "<a href='".$data['figure_amazon_ca']."'><img src='".INFUSIONS."figurelib/images/flags/flag_canada.png"."' alt='".trimlink($data['figure_amazon_ca'],50)."' title='".trimlink($data['figure_amazon_ca'],100)."'></td>\n"; }	
			 			 	 
				echo "<td align='center'>\n";	 
			 		if ($data['figure_amazon_uk'] == "") { echo "<img src='".INFUSIONS."figurelib/images/flags/flag_great_britain_sw.png"."' alt='".$locale['figure_026a']."' title='".$locale['figure_026a']."'>";
						} else { echo "<a href='".$data['figure_amazon_ca']."'><img src='".INFUSIONS."figurelib/images/flags/flag_great_britain.png"."' alt='".trimlink($data['figure_amazon_ca'],50)."' title='".trimlink($data['figure_amazon_ca'],100)."'></td>\n"; }	
				
				echo "<td align='center'>\n";	 
			 		if ($data['figure_amazon_de'] == "") { echo "<img src='".INFUSIONS."figurelib/images/flags/flag_germany_sw.png"."' alt='".$locale['figure_025a']."' title='".$locale['figure_025a']."'>";
						} else { echo "<a href='".$data['figure_amazon_de']."'><img src='".INFUSIONS."figurelib/images/flags/flag_germany.png"."' alt='".trimlink($data['figure_amazon_de'],50)."' title='".trimlink($data['figure_amazon_de'],100)."'></td>\n"; }	
				echo "</tr>\n";
			 		 
		// FIFTH LINE AMAZON JP FR ES IT
				echo "<tr>\n";
				echo "<colgroup><col width='25%'><col width='25%'><col width='25%'><col width='25%'></colgroup>\n"; 
			

				echo "<td style='text-align:center; vertical-align:middle;'>	\n";
					if ($data['figure_amazon_jp'] == "") { echo "<img src='".INFUSIONS."figurelib/images/flags/flag_japan_sw.png"."' alt='".$locale['figure_030a']."' title='".$locale['figure_030a']."'>";
						} else { echo "<a href='".$data['figure_amazon_jp']."'><img src='".INFUSIONS."figurelib/images/flags/flag_japan.png"."' alt='".trimlink($data['figure_amazon_jp'],50)."' title='".trimlink($data['figure_amazon_jp'],100)."'></td>\n"; }		
			
				echo "<td align='center'>\n";
					if ($data['figure_amazon_fr'] == "") { echo "<img src='".INFUSIONS."figurelib/images/flags/flag_france_sw.png"."' alt='".$locale['figure_027a']."' title='".$locale['figure_027a']."'>";
						} else { echo "<a href='".$data['figure_amazon_fr']."'><img src='".INFUSIONS."figurelib/images/flags/flag_france.png"."' alt='".trimlink($data['figure_amazon_fr'],50)."' title='".trimlink($data['figure_amazon_fr'],100)."'></td>\n"; }	
			 		 
				echo "<td align='center'>\n";
			 		if ($data['figure_amazon_es'] == "") { echo "<img src='".INFUSIONS."figurelib/images/flags/flag_spain_sw.png"."' alt='".$locale['figure_028a']."' title='".$locale['figure_028a']."'>";
						} else { echo "<a href='".$data['figure_amazon_es']."'><img src='".INFUSIONS."figurelib/images/flags/flag_spain.png"."' alt='".trimlink($data['figure_amazon_es'],50)."' title='".trimlink($data['figure_amazon_es'],100)."'></td>\n"; }	
				
				echo "<td align='center'>\n";
			 		if ($data['figure_amazon_it'] == "") { echo "<img src='".INFUSIONS."figurelib/images/flags/flag_italy_sw.png"."' alt='".$locale['figure_029a']."' title='".$locale['figure_029a']."'>";
						} else { echo "<a href='".$data['figure_amazon_it']."'><img src='".INFUSIONS."figurelib/images/flags/flag_italy.png"."' alt='".trimlink($data['figure_amazon_it'],50)."' title='".trimlink($data['figure_amazon_it'],100)."'></td>\n"; }	
				echo "</tr></table>\n";			
closeside();
// ############################################################################################
				
// ####### USERFIGURES  #######################################################################
			
	openside("<div class='well clearfix'><strong>".$locale['userfigure_005']."</strong></div>");	
	
	if (iMEMBER) {
		global $userdata;
							
			$resultuf = dbquery(
				"SELECT             
					fu.user_id, 
					fu.user_name, 
					fu.user_status, 
					fu.user_avatar, 
					fuf.figure_userfigures_figure_id,
					fuf.figure_userfigures_user_id          
            FROM ".DB_FIGURE_USERFIGURES." fuf
            INNER JOIN ".DB_USERS." fu ON fuf.figure_userfigures_user_id=fu.user_id  
            WHERE fuf.figure_userfigures_figure_id='".$data['figure_id']."'
            AND fu.user_id='".$userdata['user_id']."' 
			GROUP BY fu.user_id 
            ");
								
				$rows = dbrows($resultuf);

			// USER HAVE THE FIGURE
			if ($rows > 0) { 
						
					while ($datauf = dbarray($resultuf)) {
													
						if (isset($_POST['delete_from_collection'])) {
									
									dbquery("
										DELETE FROM ".DB_FIGURE_USERFIGURES." 
										WHERE figure_userfigures_figure_id=".$data['figure_id']." 
										AND figure_userfigures_user_id=".$userdata['user_id']." 
										");									
									redirect(clean_request("", array("delete_from_collection"), FALSE));

						}
					}
							echo openform('inputform', 'post', FUSION_REQUEST, array("class" => "",));
								echo "<div align='center'>\n";
								echo form_button("delete_from_collection", $locale['userfigure_002'], $locale['userfigure_002'], array("class" => "btn btn-sm btn-danger"));
								echo "  <a href='".INFUSIONS."figurelib/mycollection.php' class='btn btn-sm btn-primary'>".$locale['userfigure_006']."</a>";								
								//echo "  <a href='http://google.com' class='btn btn-sm btn-primary'>".$locale['sale']."</a>";
								echo "<p>";
								echo "</div>\n";
							echo closeform();
							

			// USER DOSEN'T HAVE THE FIGURE					
			} else { 
									
						if (isset($_POST['add_to_collection'])) {
											
							// Standard Values for Fields
							$inputArray = array(
							"figure_userfigures_figure_id" => $data['figure_id'], 
							"figure_userfigures_figure_id" => $userdata['user_id'], 
							);

							// SAVE DATA
							if (defender::safe()) {
									$inputArray = array(
										"figure_userfigures_figure_id" => $data['figure_id'],
										"figure_userfigures_user_id" => $userdata['user_id'],);
									dbquery_insert(DB_FIGURE_USERFIGURES, $inputArray, "save", array());	
									redirect(clean_request("", array("add_to_collection"), FALSE));									
							}
						}
					
							echo openform('inputform', 'post', FUSION_REQUEST, array("class" => "",));
								echo "<div align='center'>\n";
								echo form_button("add_to_collection", $locale['userfigure_001'], $locale['userfigure_001'], array("class" => "btn btn-sm btn-success"));
								echo "  <a href='".INFUSIONS."figurelib/mycollection.php' class='btn btn-sm btn-primary'>".$locale['userfigure_006']."</a>";								
								//echo "  <a href='http://google.com' class='btn btn-sm btn-primary'>".$locale['sale']."</a>";
								echo "</div>\n";
								echo "<p>";
							echo closeform();															
					}						
	}
	
// ########### 	LIST OF ALL USER WHERE HAVE THIS FIGURE  ##################################				
global $userdata;





/*        $resultufc = dbquery(
				"SELECT             
					fu.user_id, 
					fu.user_name, 
					fu.user_status, 
					fu.user_avatar, 
					fuf.figure_userfigures_figure_id,
					fuf.figure_userfigures_user_id          
            FROM ".DB_FIGURE_USERFIGURES." fuf
            INNER JOIN ".DB_USERS." fu ON fuf.figure_userfigures_user_id=fu.user_id  
            WHERE fuf.figure_userfigures_figure_id='".$data['figure_id']."'
            AND fu.user_id='".$userdata['user_id']."' 
			GROUP BY fu.user_id 			
            ");	
*/

			$resultufc = dbquery(
				"SELECT             
					fu.user_id, 
					fu.user_name, 
					fu.user_status, 
					fu.user_avatar, 
					fuf.figure_userfigures_figure_id,
					fuf.figure_userfigures_user_id          
            FROM ".DB_FIGURE_USERFIGURES." fuf
            INNER JOIN ".DB_USERS." fu ON fuf.figure_userfigures_user_id=fu.user_id  
            WHERE fuf.figure_userfigures_figure_id='".$data['figure_id']."'
			GROUP BY fu.user_id 			
            ");	
			
		if (dbrows($resultufc) != 0) {
				echo "<div align='center'>\n";
				echo $locale['userfigure_003'];		
				//echo "<p>";	
				echo "</div>";	
				
			while ($data = dbarray($resultufc)) {
				
				echo "<tr><td class='side-small'>\n";
				echo "<a href='".BASEDIR."profile.php?lookup=".$data['user_id']."' title='".$data['user_name']."'>\n";
				echo "<span>".THEME_BULLET." ".trimlink($data['user_name'], 15)."</a></span>\n";
				echo "</td></tr>\n";
				
			}

		} else {
				echo "<div align='center'>\n";
				echo $locale['userfigure_004'];	
				echo "<p>";
				echo "</div>";				
		}	
closeside();
// ############################################################################################

// ###########  RELATED FIGURES  ##############################################################			
	$fil_settings = get_settings("figurelib");
	if ($fil_settings['figure_related']) { // RELATED FIGURES
		openside("<div class='well clearfix'><strong>RELATED FIGURES</strong></div>");	
					
				//echo "<div class='panel panel-default'>\n";

					$result3 = dbquery("
                        SELECT 
                            f.figure_id, 
                            f.figure_title, 
                            f.figure_datestamp, 
                            f.figure_visibility, 
                            fc.figure_cat_id, 
                            fc.figure_cat_name,
                            fm.figure_manufacturer_id,
                            fm.figure_manufacturer_name                         
                        FROM ".DB_FIGURE_ITEMS." f 
                        INNER JOIN ".DB_FIGURE_CATS." fc ON f.figure_cat=fc.figure_cat_id
                        INNER JOIN ".DB_FIGURE_MANUFACTURERS." fm ON f.figure_manufacturer=fm.figure_manufacturer_id                        
                        WHERE MATCH (figure_title) AGAINST ('".$data['figure_title']."' IN BOOLEAN MODE) 
                        AND figure_id != '".$data['figure_id']."' ".(iSUPERADMIN ? "" : "AND ".groupaccess('figure_visibility'))." 
                        ORDER BY RAND() LIMIT 0,5");
						

				
					
					if (dbrows($result3)) {
						$i = 0;
												
							echo "<table width='100%' cellspacing='1' cellpadding='0' class=''>\n";	
							echo "<tbody><tr>\n";
							echo "<th class='' align='' width='30%'>".$locale['figure_411']."</th>\n";
							echo "<th class='' align='' width='30%'>".$locale['figure_417']."</th>\n";
							echo "<th class='' align='' width='30%'>".$locale['figure_413']."</th>\n";
							echo "<th class='' align='' width='10%'>".$locale['figure_418']."</th>\n";
							echo "</tr>\n";
						
						while ($data3 = dbarray($result3)) {

							$drating = dbarray(dbquery("SELECT SUM(rating_vote) sum_rating, COUNT(rating_item_id) count_votes FROM ".DB_RATINGS." WHERE rating_item_id='".$data3['figure_id']."' AND rating_type='FI'"));
							$num_votes = $drating['count_votes'];
							$rating = ($num_votes > 0 ? str_repeat("<img src='".INFUSIONS."figurelib/images/starsmall.png'>",ceil($drating['sum_rating']/$num_votes)) : "-");
							$cell_color = ($i % 2 == 0 ? "tbl1" : "tbl2"); $i++;
							echo "<tr>\n";
							echo "<td class='$cell_color' width='50%'><a href='figure.php?figure_id=".$data3['figure_id']."' title='".$data3['figure_title']."'>".$data3['figure_title']."</a></td>\n";
							echo "<td class='$cell_color' width='25%' align=''>".trimlink($data3['figure_manufacturer_name'],30)."</td>\n";
							echo "<td class='$cell_color' width='25%' align=''>".$data3['figure_cat_name']."</td>\n";
							echo "<td class='$cell_color' width='25%' align=''>".$rating."</td>\n";
							echo "</tr>\n";
						} 
					
					} else {
							// ['figure_426'] = "No related figures found.";
							echo "<tr><td class='tbl1' colspan='4' width='33%' align=''>".$locale['figure_426']."</td><br><br></tr>"; 

					}
							//echo "</div>\n";
							echo "</tbody></table>\n";
		closeside();										
	}					
// ############################################################################################

// ######  RATING UND COMMENTS	###############################################################
	
	$fil_settings = get_settings("figurelib");
	if ($fil_settings['figure_allow_comments']) { // Comments
		openside("<div class='well clearfix'><strong>COMMENTS</strong></div>");	
		showcomments("FI", DB_FIGURE_ITEMS, "figure_id", $_GET['figure_id'], INFUSIONS."figurelib/figures.php?figure_id=".$_GET['figure_id']);
		closeside();
	}
		
	if ($fil_settings['figure_allow_ratings']) { // Ratings
		openside("<div class='well clearfix'><strong>RATINGS</strong></div>");
		showratings("FI", $_GET['figure_id'], INFUSIONS."figurelib/figures.php?figure_id=".$_GET['figure_id']);
		closeside();
	}
	
// ####### SOCIAL_SHARING   ###################################################################	
															
						// SETTINGS HOLEN
						//$settings = fusion_get_settings();
						$settings = fusion_get_settings();
						$fil_settings = get_settings("figurelib"); 

						if ($fil_settings['figure_social_sharing']) {
						openside("<div class='well clearfix'><strong>SOCIAL SHARING</strong></div>");	
							echo "<div style='text-align:center'>\n";
							$link = $settings['siteurl'].str_replace("../","",INFUSIONS)."figurelib/figure.php?figure_id=".$_GET['figure_id'];
							echo "<a href='http://www.facebook.com/share.php?u=".$link."' target='_blank'><img alt='Facebook' src='".INFUSIONS."figurelib/images/facebook.png' border='0'></a>&nbsp;\n";
							echo "<a href='http://twitter.com/share?url=".$link."' target='_blank'><img alt='Twitter' src='".INFUSIONS."figurelib/images/twitter.png' border='0'></a>&nbsp;\n";
							echo "<a href='http://digg.com/submit?url=".$link."' target='_blank'><img alt='Digg' src='".INFUSIONS."figurelib/images/digg.png' border='0'></a>&nbsp;\n";
							echo "<a href='http://reddit.com/submit?url=".$link."' target='_blank'><img alt='Reddit' src='".INFUSIONS."figurelib/images/reddit.png' border='0'></a>&nbsp;\n";
							echo "<a href='http://del.icio.us/post?url=".$link."' target='_blank'><img alt='Del.icio.us' src='".INFUSIONS."figurelib/images/delicious.png' border='0'></a>&nbsp;\n";
							echo "</div>\n";
						closeside();
						}	

// ############################################################################################		

	} 				// TO LINE 75
}					// TO LINE 74

// ############################################################################################
	
	
// ##### PRINT BUTTON #########################################################################
//echo "<a title='".$locale['news_0002']."' href='".$info['print_link']."'><i class='entypo print'></i></a>";
//echo "<a class='m-r-10' title='".$locale['news_0002']."' href='".$info['print_link']."'><i class='entypo print'></i></a>";
//echo "<a class='m-r-10' title='".$locale['news_0002']."' href='".BASEDIR."print.php?type=F&amp;item_id=".$data['figure_id']."'><i class='entypo print'></i></a>";
// ############################################################################################

echo "</aside>\n";

	} 				// TO LINE 31
	
} 					// TO LINE 32
	
// ##### FUNCTINS FOR IMAGES  #################################################################

function get_figure_image_path($figure_images_image, $figure_images_thumb, $hiRes = FALSE) {
    if (!$hiRes) {
        if ($figure_images_thumb && file_exists(THUMBS_FIGURES.$figure_images_thumb)) {
            return THUMBS_FIGURES.$figure_images_thumb;
        }
		if ($figure_images_image && file_exists(IMAGES_FIGURES.$figure_images_image)) {
            return IMAGES_FIGURES.$figure_images_image;
        }		
    } else {
        if ($figure_images_thumb && file_exists(THUMBS_FIGURES.$figure_images_thumb)) {
            return THUMBS_FIGURES.$figure_images_thumb;
        }
		if ($figure_images_image && file_exists(IMAGES_FIGURES.$figure_images_image)) {
            return IMAGES_FIGURES.$figure_images_image;
        }
    }

}
// ############################################################################################
