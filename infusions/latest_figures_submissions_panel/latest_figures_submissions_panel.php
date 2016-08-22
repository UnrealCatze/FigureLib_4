<?php
/***************************************************************************
 *   LATEST FIGURES SUBMISSIONS PANEL                                           *
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
$locale['LFS_0001']= "Figures Submissions";
$locale['LFS_0002']= "No Submissions";
$locale['LFS_0003']= "Submission";
$locale['LFS_0004']= "Submissions";

$submits = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe='0'");	

openside($locale['LFS_0001']);

	echo "<center><div class='row'>\n";	
			
			echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";	
				echo "<a href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_submissions'><img alt='FigureLib' src='".BASEDIR."administration/images/figurelib.png' border='0'></a>";
			echo "</div>\n";
			
			echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";	
	
				if ($submits > "0") {
					
							if ($submits == "1") {
								echo " <span valign='' align='center'><font color='red'>".$submits."</font> ".$locale['LFS_0003']."</span>\n";
								} else {
								echo "<span class='' valign='' align='center'><font color='red'>".$submits."</font> ".$locale['LFS_0004']."</span>\n";	
								}
				} else {

						echo "<span>".$locale['LFS_0002']."</span>";		
				} 
	
			echo "</div>\n";
	
	echo "</div>\n";
				
				


closeside();
 
