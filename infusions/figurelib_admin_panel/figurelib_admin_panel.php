<?php
/***************************************************************************
 *   FIGURELIB PANEL                                                       *
 *                                                                         *
 *   Copyright (C) 2016 Catzenjaeger                                       *
 *   www.AlienCollectors.com                                               *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 ***************************************************************************/
if (!defined("IN_FUSION")) { die("Access Denied"); }
include INFUSIONS."figurelib/infusion_db.php";
require_once INCLUDES."infusions_include.php";
global $aidlink;

// LOCALE
$locale['LFS_0001']= "FigureLib Admin";
$locale['LFS_0002']= "-";
$locale['LFS_0003']= "Submission";
$locale['LFS_0004']= "Submissions";
$locale['LFS_0005']= "Current";
$locale['LFS_0006']= "Add";
$locale['LFS_0007']= "Categories";
$locale['LFS_0008']= "Manufacturer";
$locale['LFS_0009']= "Brands";
$locale['LFS_0010']= "Submits";
$locale['LFS_0011']= "Images";
$locale['LFS_0012']= "Settings";
$locale['LFS_0013']= "Pendings";

opensidex("<i class='fa fa-cog fa-vtop'> ".$locale['LFS_0001']."</i>");

	echo "<div class='row'>\n";	
		echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";						
				
				echo "<center><a href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib'><img alt='FigureLib' src='".BASEDIR."administration/images/figurelib.png' border='0'></a>";
				echo "</div></center>\n";
				
	echo "</div>\n";	
	
	echo "<hr>"; 
	
	echo "<div class='row'>\n";		
	echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";			
		echo "<ul>\n";
			
			// ['LFS_0005']= "Current Figures";		
				$allFiguresCounter = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe='1'");
				echo "<li><a class='side' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib'>".$locale['LFS_0005']." </a><span class='pull-right'>".$allFiguresCounter."</span></li>\n";
				
			
			// ['LFS_0007']= "Categories";	
				$allCatsCounter = dbcount("(figure_cat_id)", DB_FIGURE_CATS);
				echo "<li><a class='side' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_categories'>".$locale['LFS_0007']." </a><span class='pull-right'>".$allCatsCounter."</span></li>\n";
									
			// ['LFS_0008']= "Manufacturer";
				$allManCounter = dbcount("(figure_manufacturer_id)", DB_FIGURE_MANUFACTURERS);
				echo "<li><a class='side' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_manufacturers'>".$locale['LFS_0008']." </a><span class='pull-right'>".$allManCounter."</span></li>\n";
					
			// ['LFS_0009']= "Brands";	
				$allBrandCounter = dbcount("(figure_brand_id)", DB_FIGURE_BRANDS);	
				echo "<li><a class='side' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_brands'>".$locale['LFS_0009']." </a><span class='pull-right'>".$allBrandCounter."</span></li>\n";
								
			// ['LFS_0004']= "Submissions";							
				echo "<li><a class='side' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_submissions'>".$locale['LFS_0004']." ";	
					
			/*	$submits = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe='0'");
				if ($submits > "0") {
						
								if ($submits == "1") {
									echo "<button class='btn btn-danger btn-xs pull-right' type='button'><span class='badge'>".$submits."</span></button>\n";
									} else {
									echo "<button class='btn btn-danger btn-xs pull-right' type='button'><span class='badge'>".$submits."</span></button></a>\n";	
									}
					} else {
						
						echo "<button class='btn btn-success btn-xs pull-right' type='button'><span class='badge'>0</span></button></a>\n";
						
						//Alternative: <span class='badge badge-success badge-xs pull-right'>0</span>
									
					}
			*/

			$submits = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe='0'");
				if ($submits > "0") {
						
								if ($submits == "1") {
									echo "<span class='label label-warning pull-right'>".$submits."</span></a>\n";
									} else {
									echo "<span class='label label-danger pull-right'>".$submits."</span></a>\n";	
									}
					} else {
						
						echo "<span class='label label-success pull-right'>0</span></a>\n";
						
						//Alternative: <span class='badge badge-success badge-xs pull-right'>0</span>
									
					} 

				// ['LFS_0013']= "Pendings";						
				echo "<li><a class='side' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_pendings'>".$locale['LFS_0013']." ";	
					
				$pendings = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe='2'");	
					
				echo "<span class='label label-info pull-right'>".$pendings."</span></a>\n";	
					
		
				// ['LFS_0011']= "Images";	
				$allImgCounter = dbcount("(figure_images_image_id)", DB_FIGURE_IMAGES);	
				echo "<li><a class='side' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib'>".$locale['LFS_0011']." </a><span class='pull-right'>".$allImgCounter."</span></li>\n";

///////////////////////////////////////////////////////////////
	echo "<hr>";
		
				// ['LFS_0012']= "Settings";	
				//echo "<center><span class='glyphicon glyphicon-wrench' aria-hidden='true'><br><a class='side' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_settings'>".$locale['LFS_0012']." </a></span></center>\n";				
								
				echo "<li><center><a class='side' href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_settings'><i class='fa fa-cog' aria-hidden='true'> ".$locale['LFS_0012']." </i></center></li></a>\n";
	
		echo "</ul>\n";									
	echo "</div>\n";
	echo "</div>\n";	
					
closesidex();

