<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: MyFigureBook
| Author: Catzenjaeger
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
            AND figure_images_figure_id='".$figure_id."' 
			AND fu.user_id='".$userdata['user_id']."' 
			GROUP BY fu.user_id 
            ");
			

			
// IMAGES ####################################################################################	

	openside("");

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
				$resultimage = dbquery("
				SELECT 
					fu.user_id, 
					fu.user_name, 
					fu.user_status, 
					fu.user_avatar, 
					fuf.figure_userfigures_figure_id,
					fuf.figure_userfigures_user_id ,
					fi.figure_images_image_id,
					fi.figure_images_figure_id,
					fi.figure_images_image,
					fi.figure_images_thumb 					
				FROM ".DB_FIGURE_IMAGES." fi ON fi.figure_images_figure_id='".$figure_id."' 
				INNER JOIN ".DB_FIGURE_USERFIGURES." 
				INNER JOIN ".DB_USERS." fu ON fuf.figure_userfigures_user_id='".$user_id."'
				");
		
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

closeside();


} else {
				echo "<div align='center'>\n";
				echo $locale['userfigure_004'];	
				echo "<p>";
				echo "</div>";				
		}	
	
// ##### FUNCTIONS FOR IMAGES  #################################################################

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
require_once THEMES."templates/footer.php";