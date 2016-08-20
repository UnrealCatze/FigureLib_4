<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusions_db.php
| Author: Catzenjaeger, Frederick MC Chan
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

// CONSTANTS
// LANGUAGE
if (!defined("FIGURELIB_LOCALE")) {
    if (file_exists(INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php")) {
        define("FIGURELIB_LOCALE", INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php");
    } else {
        define("FIGURELIB_LOCALE", INFUSIONS."figurelib/locale/English/locale_figurelib.php");
    }
}


// DB TABLES
if (!defined("DB_FIGURE_ITEMS")) {
	define("DB_FIGURE_ITEMS", DB_PREFIX."figure_items");
}
if (!defined("DB_FIGURE_CATS")) {
	define("DB_FIGURE_CATS", DB_PREFIX."figure_cats");
}
if (!defined("DB_FIGURE_MANUFACTURERS")) {
	define("DB_FIGURE_MANUFACTURERS", DB_PREFIX."figure_manufacturers");
}
if (!defined("DB_FIGURE_BRANDS")) {
	define("DB_FIGURE_BRANDS", DB_PREFIX."figure_brands");
}
if (!defined("DB_FIGURE_MATERIALS")) {
	define("DB_FIGURE_MATERIALS", DB_PREFIX."figure_materials");
}
if (!defined("DB_FIGURE_SCALES")) {
	define("DB_FIGURE_SCALES", DB_PREFIX."figure_scales");
}
if (!defined("DB_FIGURE_POAS")) {
	define("DB_FIGURE_POAS", DB_PREFIX."figure_poas");
}
if (!defined("DB_FIGURE_PACKAGINGS")) {
	define("DB_FIGURE_PACKAGINGS", DB_PREFIX."figure_packagings");
}
if (!defined("DB_FIGURE_LIMITATIONS")) {
	define("DB_FIGURE_LIMITATIONS", DB_PREFIX."figure_limitations");
}
//if (!defined("DB_FIGURE_SETTINGS")) {
//	define("DB_FIGURE_SETTINGS", DB_PREFIX."figure_settings");
//}
if (!defined("DB_FIGURE_MEASUREMENTS")) {
	define("DB_FIGURE_MEASUREMENTS", DB_PREFIX."figure_measurements");
}
if (!defined("DB_FIGURE_USERFIGURES")) {
	define("DB_FIGURE_USERFIGURES", DB_PREFIX."figure_userfigures");
}
if (!defined("DB_FIGURE_IMAGES")) {
	define("DB_FIGURE_IMAGES", DB_PREFIX."figure_images");
}
if (!defined("DB_FIGURE_YEARS")) {
	define("DB_FIGURE_YEARS", DB_PREFIX."figure_years");
}
$inf_folder = "figurelib";
	
	// FOLDERS
if (!defined("FIGURES")) {
	define("FIGURES", INFUSIONS.$inf_folder."/figures/");
}
if (!defined("IMAGES_FIGURES")) {
	define("IMAGES_FIGURES", INFUSIONS.$inf_folder."/figures/images/");
}
if (!defined("THUMBS_FIGURES")) {
	define("THUMBS_FIGURES", INFUSIONS.$inf_folder."/figures/images/thumbs/");
}

// Settings
if (FUSION_SELF != "infusions.php") {
	$asettings = array();
	$aresult = dbquery("SELECT * FROM ".DB_SETTINGS_INF);
	if (dbrows($aresult)) {
		while ($adata = dbarray($aresult)) {
			$asettings[$adata['settings_name']] = $adata['settings_value'];
		}
	}
}


