<?php
/***************************************************************************
 *   LATEST FIGURES CENTER PANEL                                           *
 *                                                                         *
 *   Copyright (C) 2016 Catzenjaeger                                       *
 *   www.aliencollectors.com                                               *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 ***************************************************************************/
if (!defined("IN_FUSION")) { die("Access Denied"); }
global $aidlink, $userdata;

include INFUSIONS."figurelib/infusion_db.php";
require_once INCLUDES."infusions_include.php";
require_once INFUSIONS."figurelib/_functions.inc.php";

// LANGUAGE
if (file_exists(INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php")) {
	include INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php";
} else {
	include INFUSIONS."figurelib/locale/English/locale_figurelib.php";
}
				
// DATENBANK AUSLESEN UND DATEN BEREITSTELLEN
$result = dbquery("
	SELECT 
		tb.figure_id, tb.figure_submitter, tb.figure_freigabe, tb.figure_pubdate, tb.figure_scale, tb.figure_title, tb.figure_manufacturer, tb.figure_brand, tb.figure_datestamp, tb.figure_cat, 
		tbc.figure_cat_id, tbc.figure_cat_name, 
		tbu.user_id, tbu.user_name, tbu.user_status, tbu.user_avatar, 
		tbm.figure_manufacturer_name, 
		tbb.figure_brand_name, 
		tbs.figure_scale_id, tbs.figure_scale_name 
	FROM ".DB_FIGURE_ITEMS." tb
	LEFT JOIN ".DB_USERS." tbu ON tb.figure_submitter=tbu.user_id
	LEFT JOIN ".DB_FIGURE_CATS." tbc ON tb.figure_cat=tbc.figure_cat_id
	LEFT JOIN ".DB_FIGURE_MANUFACTURERS." tbm ON tbm.figure_manufacturer_id = tb.figure_manufacturer
	LEFT JOIN ".DB_FIGURE_BRANDS." tbb ON tbb.figure_brand_id = tb.figure_brand
	LEFT JOIN ".DB_FIGURE_SCALES." tbs ON tbs.figure_scale_id = tb.figure_scale
	".(multilang_table("FI") ? "WHERE figure_language='".LANGUAGE."' AND" : "WHERE")." tb.figure_freigabe='1' 
	ORDER BY figure_datestamp DESC LIMIT 0,5
");
			
// PANEL ÖFFNEN / ANFANG		
//openside($locale['lastentries']);
opentable("Last Figures");
 	 
// WENN DATEN UNGLEICH = 0 DANN DARSTELLUNG DER DATEN
if (dbrows($result) != 0) {

	echo "<hr>";	

	/*//// GRID SYSTEM FOR PANEL AS OVERVIEW ///////////////////////////////////////
	echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";		image
	echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";		title
	echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";		manufacturer
	echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";	brand
	echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";	scale
	echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'>\n";	year
	echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";	rating
	echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";		edit (only for admins)
	echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";		add/remove
	//////////////////////////////////////////////////////////////////////////////*/
			
	echo "<div class='navbar-default'>";				
	echo "<div class='container-fluid'>\n";
	echo "<div class='table-responsive'>\n";
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
		// COLUMN 8 (rating)
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
	echo "</div>\n";
	echo "</div>\n";
	echo "</div>\n";
	echo "<hr>";
		 
	while($data = dbarray($result)) {		
		echo "<div class='container-fluid'>\n";
		echo "<div class='table-responsive'>\n";
		echo "<div class='row'>\n";	
				
		// Get Image
		$imageURL = figures_getImagePath("figure", "thumb2", $data['figure_id']);
			
		// COLUMN 1 (image clickable)
		echo "<div class='col-lg-1 col-md-2 col-sm-2 col-xs-2'>\n";	
			echo "<div class='side-small figurelib-inforow-height'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>\n<img src='".$imageURL."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' /></a>";
		echo "</div></div>\n";				

		// COLUMN 2 (name of figure)
		echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";
			echo "<div class='side-small figurelib-inforow-height'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."'>".trimlink($data['figure_title'], 10)."</a>";
			echo "</div></div>\n";	

		// COLUMN 3 (manufacturer)
		echo "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-4'>\n";	
			echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_003']." : ".$data['figure_manufacturer_name']."' alt='".$locale['CLFP_003']." : ".$data['figure_manufacturer_name']."'>".trimlink($data['figure_manufacturer_name'],12)."</div>\n";
		echo "</div>\n";
		
		// COLUMN 4 (brand)
		echo "<div class='col-lg-2 hidden-md hidden-sm hidden-xs'>\n";
			echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_004']." : ".$data['figure_brand_name']."' alt='".$locale['CLFP_004']." : ".$data['figure_brand_name']."'>".trimlink($data['figure_brand_name'],12)."</div>\n";
		echo "</div>\n";
		
		// COLUMN 5 (scale)
		echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
			echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_005']." : ".$data['figure_scale_name']."' alt='".$locale['CLFP_005']." : ".$data['figure_scale_name']."'>".trimlink($data['figure_scale_name'],6)."</div>\n";
		echo "</div>\n";

		// COLUMN 6 (release date)
		// No release date or unknown = "no data" / WENN KEIN WERT ZUM DATUM IN DB DANN ZEIGE HINWEIS "NO DATA"
		if ($data['figure_pubdate'] == "") {
			echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'><div class='side-small figurelib-inforow-height' title='".$locale['CLFP_006']." : ".$locale['CLFP_008']."' alt='".$locale['CLFP_006']." : ".$locale['CLFP_008']."'>".trimlink($locale['CLFP_008'],6)."</div></div>\n";
		} else {
			echo "<div class='col-lg-1 col-md-2 hidden-sm hidden-xs'><div class='side-small figurelib-inforow-height' title='".$locale['CLFP_006']." : ".$data['figure_pubdate']."' alt='".$locale['CLFP_006']." : ".$data['figure_pubdate']."'>".trimlink($data['figure_pubdate'],7)."</div></div>\n";
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
			echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_010']."' alt='".$locale['CLFP_010']."'>".$rating."</div>\n";
		echo "</div>\n";
		
		// COLUMN 8 (edit) only for admins
		
		if (iADMIN || iSUPERADMIN) {
			echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";	
				echo "<span class='figurelib-inforow-height'>\n";			
					global $aidlink;
					$settings = fusion_get_settings();
					// ['cifg_0005'] = "Edit";
					echo "<a class='' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_form&amp;action=edit&amp;figure_id=".$data['figure_id']."'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a>"; 
				echo "</span>\n";
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
				echo "<div class='side-small figurelib-inforow-height' title='".$locale['CLFP_020']." : ".$count."' alt='".$locale['CLFP_020']." : ".$count."'>".trimlink($count,6)."</div>\n";
			echo "</div>\n";	
		}		

		
		// COLUMN 9 (add/remove)
		echo "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>\n";
			echo "<div class='figurelib-inforow-height'>\n";
				
				if (iMEMBER || iADMIN || iSUPERADMIN){
				echo figures_displayMyCollectionForm($data['figure_id']);				
				} else {
					
				
					
				echo "<div class='glyphicon glyphicon-eye-close' data-toggle='tooltip' data-placement='glyphicon glyphicon-eye-close' title='".$locale['mc_0011']."'></div>";
				}
			echo "</div>\n";
		echo "</div>\n";
		
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
	}


	
} else {
	echo "<div style='text-align: center;'>".$locale['CLFP_001']."</div>"; // 001 = No figures available"
}
closetable();
