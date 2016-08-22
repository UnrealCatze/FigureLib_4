/////////////////////////////////////////////////////////////////////////////////
// always find and loop ALL languages
$enabled_languages = makefilelist(LOCALE, ".|..", TRUE, "folders");
// Create a link for all installed languages
if (!empty($enabled_languages)) {
	foreach($enabled_languages as $language) {
		include LOCALE.$language."/setup.php";
		// add new language records
		$mlt_insertdbrow[$language][] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, 		link_language) VALUES ('".$locale['INF_TITLE']."', 'infusions/figurelib/figures.php', '0', '2', '0', '2', '".$language."')";
		
		$mlt_insertdbrow[$language][] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_language) VALUES ('".$locale['mycollection']."', 'infusions/figurelib/mycollection.php', ".USER_LEVEL_MEMBER.", '2', '0', '3', '".$language."')";
				
		$mlt_insertdbrow[$language][] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_language) VALUES ('".$locale['figure_521']."', 'infusions/figurelib/submit.php?stype=f', ".USER_LEVEL_MEMBER.", '2', '0', '4', '".$language."')";
		
		
		// drop deprecated language records
		$mlt_deldbrow[$language][] = DB_SITE_LINKS." WHERE link_url='infusions/figurelib/figures.php' AND link_language='".$language."'";
		$mlt_deldbrow[$language][] = DB_SITE_LINKS." WHERE link_url='infusions/figurelib/mycollection.php' AND link_language='".$language."'";
		$mlt_deldbrow[$language][] = DB_SITE_LINKS." WHERE link_url='infusions/figurelib/submit.php?stype=f' AND link_language='".$language."'";
		$mlt_deldbrow[$language][] = DB_FIGURE_CATS." WHERE figure_cat_language='".$language."'";
	}
} else {
		$inf_insertdbrow[] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_language) VALUES('".$locale['setup_3307']."', 'infusions/figurelib/figures.php', '0', '2', '0', '2', '".LANGUAGE."')";
}
/*
################################################
$enabled_languages = makefilelist(LOCALE, ".|..", TRUE, "folders");
// Create a link for all installed languages
	if (!empty($enabled_languages)) {
		foreach($enabled_languages as $language) { // these can be overriden.
					$mlt_insertdbrow[$language][] = DB_SITE_LINKS." (
						link_name, 
						link_url, 
						link_visibility, 
						link_position, 
						link_window, 
						link_order, 
						link_language
						
						) VALUES (
						
						'".$locale['INF_TITLE']."', 
						'infusions/figurelib/figures.php', 
						'0', 
						'2', 
						'0', 
						'2', 
						'".$language."'
						)";
					//WENN SUBMIT FI DANN IN CORE ÄNDERN
					$mlt_insertdbrow[$language][] = DB_SITE_LINKS." (
						link_name, 
						link_url, 
						link_visibility, 
						link_position, 
						link_window, 
						link_order, 
						link_language
						
						) VALUES (
						
						'".$locale['figure_521']."', 
						'submit.php?stype=f', 
						".USER_LEVEL_MEMBER.", 
						'1', 
						'0', 
						'14', 
						'".$language."'
						)";
					$mlt_insertdbrow[$language][] = DB_SITE_LINKS." (
						link_name, 
						link_url, 
						link_visibility, 
						link_position, 
						link_window, 
						link_order, 
						link_language
						
						) VALUES (
						
						'".$locale['figure_521']."', 
						'infusions/figurelib/submit.php', 
						".USER_LEVEL_MEMBER.", 
						'1', 
						'1', 
						'14', 
						'".$language."'
						)";
					}
				} else {
						$inf_insertdbrow[] = DB_SITE_LINKS." (
						link_name, 
						link_url, 
						link_visibility, 
						link_position, 
						link_window, 
						link_order, 
						link_language
						
						) VALUES (
						
						'".$locale['INF_TITLE']."', 
						'infusions/figurelib/figures.php', 
						'0', 
						'2', 
						'0', 
						'2', 
						'".LANGUAGE."'
						)";
	}
*/
// Automatic enable of the latest figures panel
					$inf_insertdbrow[] = DB_PANELS." (
						panel_name, 
						panel_filename, 
						panel_content, 
						panel_side, 
						panel_order, 
						panel_type, 
						panel_access, 
						panel_display, 
						panel_status, 
						panel_url_list, 
						panel_restriction
						) VALUES(
						'Latest figure panel', 
						'latest_figures_center_panel', 
						'', 
						'2', 
						'2', 
						'file', 
						'0', 
						'0', 
						'1', 
						'', 
						'0'
						)";
// so lange betaphase erstmal teilweise auskommentieren weil sond die ganzen submittings weg sind 
// und alle mühesam per hand neu eingereicht werden müssen						
						
// Defuse cleaning	
$inf_droptable[] = DB_FIGURE_CATS;
$inf_droptable[] = DB_FIGURE_ITEMS;
	// wird nicht benötigt aber erstmal lassen
	//$inf_droptable[] = DB_FIGURE_SETTINGS;
$inf_droptable[] = DB_FIGURE_MANUFACTURERS;
$inf_droptable[] = DB_FIGURE_BRANDS;
$inf_droptable[] = DB_FIGURE_MATERIALS;
$inf_droptable[] = DB_FIGURE_SCALES;
$inf_droptable[] = DB_FIGURE_POAS;
$inf_droptable[] = DB_FIGURE_PACKAGINGS;
$inf_droptable[] = DB_FIGURE_LIMITATIONS;
$inf_droptable[] = DB_FIGURE_MEASUREMENTS;
$inf_droptable[] = DB_FIGURE_USERFIGURES;
$inf_droptable[] = DB_FIGURE_IMAGES;
$inf_droptable[] = DB_FIGURE_YEARS;
$inf_deldbrow[] = DB_COMMENTS." WHERE comment_type='FI'";
$inf_deldbrow[] = DB_RATINGS." WHERE rating_type='FI'";
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='FI'";
$inf_deldbrow[] = DB_SITE_LINKS." WHERE link_url='infusions/figurelib/figures.php'";
$inf_deldbrow[] = DB_SITE_LINKS." WHERE link_url='infusions/figurelib/submit.php'";
$inf_deldbrow[] = DB_SITE_LINKS." WHERE link_url='infusions/figurelib/mycollection.php'";
$inf_deldbrow[] = DB_LANGUAGE_TABLES." WHERE mlt_rights='FI'";
$inf_deldbrow[] = DB_PANELS." WHERE panel_filename='latest_figures_center_panel'";
$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='figurelib'";
