<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: figurelib_admin based on weblinks_admin.php
| Author: PHP-Fusion Developer Team
|
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
pageAccess("FI");
require_once THEMES."templates/admin_header.php";
require_once INCLUDES."html_buttons_include.php";
require_once INCLUDES."infusions_include.php";
include INFUSIONS."figurelib/infusion_db.php";
require_once INFUSIONS."figurelib/_functions.inc.php";
global $settings;
// LANGUAGE
if (file_exists(INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php")) {
	include INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php";
} else {
	include INFUSIONS."figurelib/locale/English/locale_figurelib.php";
}
if (file_exists(LOCALE.LOCALESET."admin/settings.php")) {
	include LOCALE.LOCALESET."admin/settings.php";
} else {
	include LOCALE."English/admin/settings.php";
}
add_breadcrumb(array(
				   'link' => INFUSIONS.'figurelib/admin.php'.$aidlink.'&amp;section=figurelib',
				   'title' => 'FigureLib Admin Area'
			   ));
// SETTINGS HOLEN
$fil_settings = get_settings("figurelib");
$allowed_pages = array(
	"figurelib_form",
	"figurelib_categories",
	"figurelib_manufacturers",
	"figurelib_brands",
	//"figurelib_materials",
	//"figurelib_scales",
	//"figurelib_poas",
	//"figurelib_packagings",
	//"figurelib_limitations",
	//"figurelib_measurements",
	//"figurelib_userfigures",
	//"figurelib_images",
	"figurelib_submissions",
	"figurelib_settings"		
);
$_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_pages) ? $_GET['section'] : 'figurelib';
$figure_edit = isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['figure_id']) && isnum($_GET['figure_id']) ? TRUE : FALSE;
$figureCat_edit = isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['cat_id']) && isnum($_GET['cat_id']) ? TRUE : FALSE;
$figureMan_edit = isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['man_id']) && isnum($_GET['man_id']) ? TRUE : FALSE;
$figureBrand_edit = isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['brand_id']) && isnum($_GET['brand_id']) ? TRUE : FALSE;
$figure_id = ($figure_edit) ? intval($_GET['figure_id']) : 0;
// TAB MENÜ OBERPUNKTE

		// ['filt_0003'] = "Current Figures";
		$master_title['title'][] = $locale['filt_0003']; 
		$master_title['id'][] = 'figurelib';
		$master_title['icon'] = '';
		
		
		// ['filt_0002'] = "Edit Figures";
		// ['filt_0001'] = "Add Figures";
		$master_title['title'][] = $figure_edit ? $locale['filt_0002'] : $locale['filt_0001']; 
		$master_title['id'][] = 'figurelib_form';
		$master_title['icon'] = '';
		
		// ['filt_0005'] = "Edit Figures Category";
		// ['filt_0004'] = "Figures Categories";
		$master_title['title'][] = $figureCat_edit ? $locale['filt_0005'] : $locale['filt_0004']; 
		$master_title['id'][] = 'figurelib_categories';
		$master_title['icon'] = '';
		
		// ['filt_0013'] = "Edit Figures Manufacturer";
		// ['filt_0014'] = "Figures Manufacturer";
		$master_title['title'][] = $figureMan_edit ? $locale['filt_0013'] : $locale['filt_0014']; 
		$master_title['id'][] = 'figurelib_manufacturers';
		$master_title['icon'] = '';
		
		// ['filt_0015'] = "Edit Figures Brand";
		// ['filt_0016'] = "Figure Brand";
		$master_title['title'][] = $figureBrand_edit ? $locale['filt_0015'] : $locale['filt_0016']; 
		$master_title['id'][] = 'figurelib_brands';
		$master_title['icon'] = '';
		
		// ['filt_0009'] = "Figure Submissions";
		$submissionCounter = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe='0'");
		$master_title['title'][] = $locale['filt_0009']."  <span class='badge'>".$submissionCounter."</span>"; 
		$master_title['id'][] = 'figurelib_submissions';
		$master_title['icon'] = '';
		
		// ['filt_0010'] = "Settings
		$master_title['title'][] = $locale['filt_0010']; 
		$master_title['id'][] = 'figurelib_settings';
		$master_title['icon'] = '';
		
$tab_active = $_GET['section'];
// ['filt_0011'] = "FigureLib Admin Area";
opentable($locale['filt_0011']); 
echo opentab($master_title, $tab_active, "figurelib_admin", 1);
switch ($_GET['section']) {
	
	case "figurelib_form":
		add_breadcrumb(array('link'=>"", 'title'=>$master_title['title'][1]));
		include "admin/admin_figurelib.php";
		break;
	case "figurelib_categories":
		add_breadcrumb(array('link'=>"", 'title'=>$master_title['title'][2]));
		include "admin/admin_figurelib_categories.php";
		break;	
	case "figurelib_manufacturers":
		add_breadcrumb(array('link'=>"", 'title'=>$locale['filt_0014'])); // ['filt_0014'] = "Figures Manufacturer";
		include "admin/admin_figurelib_manufacturers.php";
		break;		
	case "figurelib_brands":
		add_breadcrumb(array('link'=>"", 'title'=>$locale['filt_0016'])); // ['filt_0016'] = "Figure Brand";
		include "admin/admin_figurelib_brands.php";
		break;		
	case "figurelib_settings":
		add_breadcrumb(array('link'=>"", 'title'=>$locale['filt_0010'])); // ['filt_0010'] = "Settings";
		include "admin/admin_figurelib_settings.php";
		break;
	case "figurelib_submissions":
		add_breadcrumb(array('link'=>"", 'title'=>$locale['filt_0009'])); // ['filt_0009'] = "Figure Submissions";
		include "admin/admin_figurelib_submissions.php";
		break;
	default:
		figurelib_listing();
}
echo closetab();
closetable();
require_once THEMES."templates/footer.php";
/**
 * Figurelib Directory Listing
 */
function figurelib_listing() {
	global $aidlink, $locale;
	// do a filter here
	$limit = 15;
	$total_rows = dbcount("(figure_id)", DB_FIGURE_ITEMS);
	$submissions = dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_freigabe = 0");
	$rowstart = isset($_GET['rowstart']) && ($_GET['rowstart'] <= $total_rows) ? $_GET['rowstart'] : 0;
	// add a filter browser
	$catOpts = array(
		"all" => $locale['filt_0012'],
	); // ['filt_0012'] = "All Figures Entries";
	
	
	//////////////////////////////////
	$categories = dbquery("
			SELECT 
				figure_cat_id, 
				figure_cat_name 
			FROM ".DB_FIGURE_CATS." ".(multilang_table("FI") ? "
			WHERE figure_cat_language='".LANGUAGE."'" : "
		"));
	
	if (dbrows($categories) > 0) {
		while ($cat_data = dbarray($categories)) {
			$catOpts[$cat_data['figure_cat_id']] = $cat_data['figure_cat_name'];
		}
	}
	/////////////////////////////////////
	
	// prevent xss
	$catFilter = "";
	if (isset($_GET['filter_cid']) && isnum($_GET['filter_cid']) && isset($catOpts[$_GET['filter_cid']])) {
		if ($_GET['filter_cid'] > 0) {
			$catFilter = "WHERE ".(multilang_table("FI") ? "cat.figure_cat_language='".LANGUAGE."' and " : "")." figure_cat='".intval($_GET['filter_cid'])."'";
		}
	}	
	
$result = dbquery("
	SELECT f.*, 
			cat.figure_cat_id, 
			cat.figure_cat_name, 
			man.figure_manufacturer_name, 
			man.figure_manufacturer_id,  
			sca.figure_scale_name, 
			sca.figure_scale_id
	FROM ".DB_FIGURE_ITEMS." f
	LEFT JOIN ".DB_FIGURE_CATS." cat on cat.figure_cat_id = f.figure_cat
	INNER JOIN ".DB_FIGURE_MANUFACTURERS." man on man.figure_manufacturer_id = f.figure_manufacturer
	INNER JOIN ".DB_FIGURE_SCALES." sca on sca.figure_scale_id = f.figure_scale
	".($catFilter ? $catFilter." AND " : "WHERE ")." figure_freigabe='1'
	ORDER by figure_title asc, figure_datestamp desc 
	LIMIT $rowstart, $limit
	");
	
	$rows = dbrows($result);
	echo "<div class='clearfix m-b-20'>\n";
	// ['figs_0002'] = "Currently displaying %d of %d total figure/s entries";
	echo "<span class='pull-right m-t-10'>".sprintf($locale['figs_0002'], $rows, $total_rows)."</span>";
	#<a href='".INFUSIONS."figurelib/admin.php".$aidlink."&amp;section=figurelib_submissions'>Submissions: ".$submissions."</span>\n";
	if (!empty($catOpts) > 0 && $total_rows > 0) {
		echo "<div class='pull-left m-t-10 m-r-10'>".$locale['cifg_0009']."</div>\n"; // ['cifg_0009'] = "Filter by:";
		echo "<div class='dropdown pull-left m-t-5 m-r-10' style='position:relative'>\n";
		echo "<a class='dropdown-toggle btn btn-default btn-sm' style='width: 200px;' data-toggle='dropdown'>\n<strong>\n";
		if (isset($_GET['filter_cid']) && isset($catOpts[$_GET['filter_cid']])) {
			echo $catOpts[$_GET['filter_cid']];
		} else {
			echo $locale['figf_0002']; // $locale['figf_0002'] = "Filter show category by";
		}
		echo " <span class='caret'></span></strong>\n</a>\n";
		echo "<ul class='dropdown-menu' style='max-height:180px; width:200px; overflow-y: auto'>\n";
		foreach ($catOpts as $catID => $catName) {
			$active = isset($_GET['filter_cid']) && $_GET['filter_cid'] == $catID ? TRUE : FALSE;
			echo "<li".($active ? " class='active'" : "").">\n<a class='text-smaller' href='".clean_request("filter_cid=".$catID, array(
					"section",
					"rowstart",
					"aid"
				), TRUE)."'>\n";
			echo $catName;
			echo "</a>\n</li>\n";
		}
		echo "</ul>\n";
		echo "</div>\n";
	}
	if ($total_rows > $rows) {
		echo makepagenav($rowstart, $limit, $total_rows, $limit, clean_request("", array(
									  "aid",
									  "section"
								  ), TRUE)."&amp;");
	}
	echo "</div>\n";
		
	if ($rows > 0) {	
		/*
				echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n"; 	Image
				echo "<div class='col-lg-1 col-md-1 col-sm-3 col-xs-12'>\n"; 	Edit
				echo "<div class='col-lg-2 col-md-3 col-sm-3 col-xs-12'>\n"; 	Titel
				echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";	ID	
				echo "<div class='col-lg-2 col-md-2 col-sm-3 col-xs-12'>\n";    Category			
				echo "<div class='col-lg-2 col-md-3 hidden-sm hidden-xs'>\n";	Manufacturer
				echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";	Scale
				echo "<div class='col-lg-1 col-md-3 col-sm-3 col-xs-12'>\n";	Submitter
				echo "<div class='col-lg-1 col-md-1 col-sm-3 col-xs-12'>\n"; 	Delete
		*/
				
				
				echo "<div class='page-header'>";
				echo "<div class='clearfix m-b-20'>\n";
				echo "<div class='table-responsive'>\n";
				echo "<div class='row'>\n";	
				echo "<div class='strong'>\n";	
		
						// COLUMN 1  $locale['figure_588'] Image
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['figure_588']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 2 ['cifg_0005'] = "Edit";
						echo "<div class='col-lg-1 col-md-1 col-sm-3 col-xs-12'>\n"; 
							echo "<div class='text-smaller text-uppercase'>".$locale['cifg_0005']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 3  ['cifg_0000'] = "Figure Name/Title";
						echo "<div class='col-lg-2 col-md-3 col-sm-3 col-xs-12'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['cifg_0000']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 4 ['cifg_0004'] = "Figure Id";
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['cifg_0004']."</div>\n";
						echo "</div>\n";

						// COLUMN 5 ['cifg_0001'] = "Category";
						echo "<div class='col-lg-2 col-md-2 col-sm-3 col-xs-12'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['cifg_0001']."</div>\n";
						echo "</div>\n";						
						
						// COLUMN 6 ['cifg_0010'] = "Manufacturer";
						echo "<div class='col-lg-2 col-md-3 hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['cifg_0010']."</div>\n";
						echo "</div>\n";	
						
						// COLUMN 7 ['cifg_0011'] = "Scale";
						echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['cifg_0011']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 8 $locale['CLFP_011'] Submitter
						echo "<div class='col-lg-1 col-md-3 col-sm-3 col-xs-12'>\n";
							echo "<div class='text-smaller text-uppercase'>".$locale['CLFP_011']."</div>\n";
						echo "</div>\n";
						
						// COLUMN 9 ['cifg_0006'] = "Delete"; 
						echo "<div class='col-lg-1 col-md-1 col-sm-3 col-xs-12'>\n"; 
							echo "<div class='text-smaller text-uppercase'>".$locale['cifg_0006']."</div>\n";
						echo "</div>\n";
						
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";				
				
				echo "<div class='clearfix m-b-20'>\n";
				echo "<div class='row'>\n";	
				echo "<div style='line-height: 60px'>";
		
					while ($data = dbarray($result)) {
			

 	
						// COLUMN 1 (image clickable)
							echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
								echo "<div class='side-small figurelib-inforow-height'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."'>\n<img src='".figures_getImagePath("figure", "thumb2", $data['figure_id'])."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' /></a>";
							echo "</div></div>\n";	


						// COLUMN 2 Edit
							echo "<div class='col-lg-1 col-md-1 col-sm-3 col-xs-12'>\n"; 
									echo "<div class='btn-group figurelib-inforow-height'>\n";				
							// ['cifg_0005'] = "Edit";
										echo "<a class='glyphicon glyphicon-edit' href='".FUSION_SELF.$aidlink."&amp;section=figurelib_form&amp;action=edit&amp;figure_id=".$data['figure_id']."' alt='".$locale['cifg_0005']."' title='".$locale['cifg_0005']."'></a>&nbsp;"; 													 						
							echo "</div></div>\n";	
												
						// COLUMN 3 Figure Name/Title
							echo "<div class='col-lg-2 col-md-3 col-sm-3 col-xs-12'>\n";
									echo "<div class='side-small figurelib-inforow-height'><a href='".INFUSIONS."figurelib/figures.php?figure_id=".$data['figure_id']."' title='".$locale['CLFP_002']." : ".$data['figure_title']."' alt='".$locale['CLFP_002']." : ".$data['figure_title']."'>".trimlink($data['figure_title'], 10)."</a>";
							echo "</div></div>\n";	
							
						// COLUMN 4 Figure ID
							echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
									echo "<div  class='side-small figurelib-inforow-height'>".trimlink($data['figure_id'], 5)."</div></div>\n";	
									
						// COLUMN 5 Category
							echo "<div class='col-lg-2 col-md-2 col-sm-3 col-xs-12'>\n";
									echo "<div class='side-small figurelib-inforow-height'>".trimlink($data['figure_cat_name'],10)."</div></div>\n";				
																	
						// COLUMN 6 Manufacturer
							echo "<div class='col-lg-2 col-md-3 hidden-sm hidden-xs'>\n";
									echo "<div class='side-small figurelib-inforow-height'>".trimlink($data['figure_manufacturer_name'],10)."</div></div>\n";	
						
						// COLUMN 7 Scale
							echo "<div class='col-lg-1 hidden-md hidden-sm hidden-xs'>\n";
								echo "<div class='side-small figurelib-inforow-height'>".trimlink($data['figure_scale_name'],10)."</div></div>\n";
						
						// COLUMN 8 Submitter
								$result2 = dbquery("SELECT	fi.figure_id, fi.figure_title, tu.user_id, tu.user_name, tu.user_avatar, tu.user_status
								FROM ".DB_FIGURE_ITEMS." fi
								LEFT JOIN ".DB_USERS." tu ON fi.figure_submitter=tu.user_id
								WHERE figure_id='".$data['figure_id']."'  order by figure_datestamp desc
									");
							while ($data2 = dbarray($result2)) {
								
								echo "<div class='col-lg-1 col-md-3 col-sm-3 col-xs-12'>\n";
									echo "<span class='small figurelib-inforow-height'>".profile_link($data2['user_id'], trimlink($data2['user_name'],10), $data2['user_status'])."<br/></span>\n";
								echo "</div>\n";	
									
							}				
																		
						// COLUMN 9 Delete
							echo "<div class='col-lg-1 col-md-1 col-sm-3 col-xs-12'>\n"; 
									echo "<div class='btn-group figurelib-inforow-height'>\n";				
							// ['cifg_0006'] = "Delete";
										echo "<a class='glyphicon glyphicon-trash'  href='".FUSION_SELF.$aidlink."&amp;section=figurelib_form&amp;action=delete&amp;figure_id=".$data['figure_id']."&amp;figure_id=".$data['figure_id']."' alt='".$locale['cifg_0006']."' title='".$locale['cifg_0006']."' onclick=\"return confirm('".$locale['film_0004']."');\"></a>&nbsp;";							
					
							echo "</div></div>\n";	
	
					}
				
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
				
	} else {
		// ['cifg_0007'] = "There are no figures defined";
		echo "<p><div class='alert alert-warning' role='alert'>";
		echo "<div class='text-center'>\n".$locale['cifg_0007']."<br />";
		echo "</div>";		
	}
}
