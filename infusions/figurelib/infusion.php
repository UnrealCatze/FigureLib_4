<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
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
include INFUSIONS."figurelib/infusion_db.php";
$locale = fusion_get_locale("", FIGURELIB_LOCALE);
// INFUSION GENERAL INFORMATION
$inf_title = $locale['INF_TITLE'];
$inf_description = $locale['INF_DESC'];
$inf_version = "1.00";
$inf_developer = "Catzenjaeger";
$inf_email = "info@aliencollectors.com";
$inf_weburl = "http://www.AlienCollectors.com";
$inf_folder = "figurelib";
$inf_image = "icon.png";
//$inf_image = "figure_ico.png";
// Position these links under Content Administration
$inf_adminpanel[] = array(
	"image" => $inf_image,
	"page" => 1,
	"rights" => "FI",
	"title" => $locale['INF_TITLE'],
	"panel" => "admin.php"
);
// Multilanguage table for Administration
$inf_mlt[] = array(
"title" => $locale['INF_ADMIN'],
"rights" => "FI",
);
// Create tables
$inf_newtable[] = DB_FIGURE_CATS." (
	figure_cat_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	figure_cat_parent MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	figure_cat_name VARCHAR(100) NOT NULL DEFAULT '',
	figure_cat_description TEXT NOT NULL,
	figure_cat_sorting VARCHAR(50) NOT NULL DEFAULT 'figure_title ASC',
	figure_cat_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
	PRIMARY KEY(figure_cat_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
/////////////////////////////////////////////////////////////////////////////////
$inf_newtable[] = DB_FIGURE_ITEMS." (
		figure_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		figure_freigabe TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
		figure_submitter VARCHAR(200) NOT NULL DEFAULT '',
		figure_agb TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
		figure_cat MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
		figure_title VARCHAR(200) NOT NULL DEFAULT '',
		figure_variant VARCHAR(200) NOT NULL DEFAULT '',
		figure_manufacturer VARCHAR(200) NOT NULL DEFAULT '',
		figure_artists VARCHAR(400) NOT NULL DEFAULT '',
		figure_country VARCHAR(200) NOT NULL DEFAULT '',
		figure_brand VARCHAR(200) NOT NULL DEFAULT '',
		figure_series VARCHAR(200) NOT NULL DEFAULT '',
		figure_scale VARCHAR(200) NOT NULL DEFAULT '',
		figure_weight VARCHAR(200) NOT NULL DEFAULT '',
		figure_height VARCHAR(200) NOT NULL DEFAULT '',
		figure_width VARCHAR(200) NOT NULL DEFAULT '',
		figure_depth VARCHAR(200) NOT NULL DEFAULT '',
		figure_material VARCHAR(200) NOT NULL DEFAULT '',
		figure_poa VARCHAR(200) NOT NULL DEFAULT '',
		figure_packaging VARCHAR(200) NOT NULL DEFAULT '',
		figure_pubdate VARCHAR(200) NOT NULL DEFAULT '',
		figure_retailprice decimal(8,2) NOT NULL,
		figure_usedprice decimal(8,2) NOT NULL,
		figure_limitation VARCHAR(200) NOT NULL DEFAULT '',
		figure_editionsize decimal(8) NOT NULL,
		figure_forum_url VARCHAR(200) NOT NULL DEFAULT '',
		figure_affiliate_1 VARCHAR(500) NOT NULL DEFAULT '',
		figure_affiliate_2 VARCHAR(500) NOT NULL DEFAULT '',
		figure_affiliate_3 VARCHAR(500) NOT NULL DEFAULT '',
		figure_affiliate_4 VARCHAR(500) NOT NULL DEFAULT '',
		figure_affiliate_5 VARCHAR(500) NOT NULL DEFAULT '',
		figure_affiliate_6 VARCHAR(500) NOT NULL DEFAULT '',
		figure_affiliate_7 VARCHAR(500) NOT NULL DEFAULT '',
		figure_affiliate_8 VARCHAR(500) NOT NULL DEFAULT '',
		figure_affiliate_9 VARCHAR(500) NOT NULL DEFAULT '',
		figure_affiliate_10 VARCHAR(500) NOT NULL DEFAULT '',
		figure_eshop VARCHAR(400) NOT NULL DEFAULT '',
		figure_amazon_de VARCHAR(400) NOT NULL DEFAULT '',
		figure_amazon_uk VARCHAR(400) NOT NULL DEFAULT '',
		figure_amazon_fr VARCHAR(400) NOT NULL DEFAULT '',
		figure_amazon_es VARCHAR(400) NOT NULL DEFAULT '',
		figure_amazon_it VARCHAR(400) NOT NULL DEFAULT '',
		figure_amazon_jp VARCHAR(400) NOT NULL DEFAULT '',
		figure_amazon_com VARCHAR(400) NOT NULL DEFAULT '',
		figure_amazon_ca VARCHAR(400) NOT NULL DEFAULT '',
		figure_accessories TEXT NOT NULL,
		figure_description TEXT NOT NULL,
		figure_visibility TINYINT(4) NOT NULL DEFAULT '0',		
		figure_datestamp int(10) UNSIGNED NOT NULL DEFAULT '0',
		figure_clickcount int(10) UNSIGNED NOT NULL DEFAULT '0',
		figure_allow_comments TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
		figure_allow_ratings TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
		figure_sorting VARCHAR(50) NOT NULL DEFAULT '',
		figure_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
		KEY figure_datestamp (figure_datestamp),
		KEY figure_clickcount (figure_clickcount),
		PRIMARY KEY (figure_id)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
/////////////////////////////////////////////////////////////////////////////////	
/*
$inf_newtable[] = DB_FIGURE_SETTINGS." (
		settings_name VARCHAR(200) NOT NULL DEFAULT '',
		settings_value TEXT NOT NULL
		settings_inf TEXT NOT NULL
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
*/	
/////////////////////////////////////////////////////////////////////////////////
$inf_newtable[] = DB_FIGURE_MANUFACTURERS." (
		figure_manufacturer_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		figure_manufacturer_name VARCHAR(100) NOT NULL DEFAULT '',
		figure_manufacturer_description TEXT NOT NULL,
		figure_manufacturer_parent MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
		figure_manufacturer_sorting VARCHAR(50) NOT NULL DEFAULT '',
		figure_manufacturer_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
		PRIMARY KEY (figure_manufacturer_id)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
/////////////////////////////////////////////////////////////////////////////////
$inf_newtable[] = DB_FIGURE_BRANDS." (
		figure_brand_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		figure_brand_name VARCHAR(100) NOT NULL DEFAULT '',
		figure_brand_description TEXT NOT NULL,
		figure_brand_parent MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
		figure_brand_sorting VARCHAR(50) NOT NULL DEFAULT '',
		figure_brand_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
		PRIMARY KEY (figure_brand_id)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
/////////////////////////////////////////////////////////////////////////////////
$inf_newtable[] = DB_FIGURE_MATERIALS." (
		figure_material_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		figure_material_name VARCHAR(100) NOT NULL DEFAULT '',
		figure_material_description TEXT NOT NULL,
		figure_material_parent MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
		figure_material_sorting VARCHAR(50) NOT NULL DEFAULT '',
		figure_material_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
		PRIMARY KEY (figure_material_id)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
/////////////////////////////////////////////////////////////////////////////////
$inf_newtable[] = DB_FIGURE_SCALES." (
		figure_scale_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		figure_scale_name VARCHAR(100) NOT NULL DEFAULT '',
		figure_scale_description TEXT NOT NULL,
		figure_scale_parent MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
		figure_scale_sorting VARCHAR(50) NOT NULL DEFAULT '',
		figure_scale_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
		PRIMARY KEY (figure_scale_id)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
/////////////////////////////////////////////////////////////////////////////////
$inf_newtable[] = DB_FIGURE_POAS." (
		figure_poa_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		figure_poa_name VARCHAR(100) NOT NULL DEFAULT '',
		figure_poa_description TEXT NOT NULL,
		figure_poa_parent MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
		figure_poa_sorting VARCHAR(50) NOT NULL DEFAULT '',
		figure_poa_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
		PRIMARY KEY (figure_poa_id)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
/////////////////////////////////////////////////////////////////////////////////
$inf_newtable[] = DB_FIGURE_PACKAGINGS." (
		figure_packaging_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		figure_packaging_name VARCHAR(100) NOT NULL DEFAULT '',
		figure_packaging_description TEXT NOT NULL,
		figure_packaging_parent MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
		figure_packaging_sorting VARCHAR(50) NOT NULL DEFAULT '',
		figure_packaging_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
		PRIMARY KEY (figure_packaging_id)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
/////////////////////////////////////////////////////////////////////////////////
$inf_newtable[] = DB_FIGURE_LIMITATIONS." (
		figure_limitation_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		figure_limitation_name VARCHAR(100) NOT NULL DEFAULT '',
		figure_limitation_description TEXT NOT NULL,
		figure_limitation_parent MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
		figure_limitation_sorting VARCHAR(50) NOT NULL DEFAULT '',
		figure_limitation_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
		PRIMARY KEY (figure_limitation_id)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
/////////////////////////////////////////////////////////////////////////////////
$inf_newtable[] = DB_FIGURE_MEASUREMENTS." (
figure_measurements_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT, 
figure_measurements_inch VARCHAR(100) NOT NULL DEFAULT '',
figure_measurements_cm VARCHAR(100) NOT NULL DEFAULT '',
figure_measurements_description TEXT NOT NULL,
figure_measurements_parent MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
figure_measurements_sorting VARCHAR(50) NOT NULL DEFAULT '',
figure_measurements_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
PRIMARY KEY (figure_measurements_id)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
/////////////////////////////////////////////////////////////////////////////////
$inf_newtable[] = DB_FIGURE_USERFIGURES." (
figure_userfigures_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT, 
figure_userfigures_figure_id VARCHAR(100) NOT NULL DEFAULT '',
figure_userfigures_user_id VARCHAR(100) NOT NULL DEFAULT '',
figure_userfigures_sorting VARCHAR(50) NOT NULL DEFAULT '',
figure_userfigures_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
PRIMARY KEY (figure_userfigures_id)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
/////////////////////////////////////////////////////////////////////////////////
$inf_newtable[] = DB_FIGURE_IMAGES." (
figure_images_image_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT, 
figure_images_figure_id VARCHAR(100) NOT NULL DEFAULT '',
figure_images_image VARCHAR(100) NOT NULL DEFAULT '',
figure_images_thumb VARCHAR(100) NOT NULL DEFAULT '',
figure_images_sorting VARCHAR(50) NOT NULL DEFAULT '',
figure_images_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
PRIMARY KEY (figure_images_image_id)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
/////////////////////////////////////////////////////////////////////////////////
$inf_newtable[] = DB_FIGURE_YEARS." (
figure_year_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT, 
figure_year VARCHAR(100) NOT NULL DEFAULT '',
figure_year_description TEXT NOT NULL,
figure_year_parent MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
figure_year_sorting VARCHAR(50) NOT NULL DEFAULT '',
figure_year_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
PRIMARY KEY (figure_year_id)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
/////////////////////////////////////////////////////////////////////////////////
// Settings
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_per_page', '10', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_per_line', '4', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_allow_comments', '1', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_allow_ratings', '1', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_display', '1', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_submit', '1', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_related', '1', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_social_sharing', '1', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_thumb_ratio', '0', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_image_upload_count', '10', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_photo_w', '800', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_photo_h', '600', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_thumb_w', '400', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_thumb_h', '300', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_photo_max_w', '1800', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_photo_max_h', '1600', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_photo_max_b', '500000', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_notification', '1', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_notification_subject', '', 'figurelib')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('figure_notification_message', '', 'figurelib')";
/////////////////////////////////////////////////////////////////////////////////
//befüllen von Brand
$inf_insertdbrow[] = DB_FIGURE_BRANDS." (figure_brand_id, figure_brand_name, figure_brand_description, figure_brand_sorting) VALUES
(1, 'Unknown', '', 'figure_id ASC'),
(2, 'Alien (1979)', '', 'figure_id ASC'),
(3, 'Aliens (1986)', '', 'figure_id ASC'),
(4, 'Alien 3 (1992)', '', 'figure_id ASC'),
(5, 'Alien: Resurrection (1997)', '', 'figure_id ASC'),
(6, 'Alien vs. Predator (2004)', '', 'figure_id ASC'),
(7, 'Aliens vs. Predator: Requiem (2007)', '', 'figure_id ASC'),
(8, 'Prometheus (2012)', '', 'figure_id ASC'),
(9, 'Alien: Covenant (2017)', '', 'figure_id ASC')
";
/////////////////////////////////////////////////////////////////////////////////
//befüllen von Scales
$inf_insertdbrow[] = DB_FIGURE_SCALES." (figure_scale_id, figure_scale_name, figure_scale_description, figure_scale_sorting) VALUES 
(1, 'Unknown', '', 'figure_id ASC'),
(2, '1:1', '', 'figure_id ASC'),
(3, '1:2', '', 'figure_id ASC'),
(4, '1:3', '', 'figure_id ASC'),
(5, '1:4', '', 'figure_id ASC'),
(6, '1:5', '', 'figure_id ASC'),
(7, '1:6', '', 'figure_id ASC'),
(8, '1:9', '', 'figure_id ASC'),
(9, '1:10', '', 'figure_id ASC'),
(10, '1:12', '', 'figure_id ASC'),
(11, '1:24', '', 'figure_id ASC')
";
/////////////////////////////////////////////////////////////////////////////////
//befüllen von measurements
$inf_insertdbrow[] = DB_FIGURE_MEASUREMENTS." (figure_measurements_id, figure_measurements_inch, figure_measurements_cm, figure_measurements_description, figure_measurements_sorting) VALUES 
(1, 'Unknown','', '', 'figure_id ASC'),
(2, '01 Inch','2,54 cm', '', 'figure_id ASC'),
(3, '02 Inch','5,08 cm', '', 'figure_id ASC'),
(4, '03 Inch','7,62 cm', '', 'figure_id ASC'),
(5, '04 Inch','9,00 cm', '', 'figure_id ASC'),
(6, '05 Inch','12,70 cm', '', 'figure_id ASC'),
(7, '06 Inch','15,24 cm', '', 'figure_id ASC'),
(8, '07 Inch','17,78 cm', '', 'figure_id ASC'),
(9, '08 Inch','20,32 cm', '', 'figure_id ASC'),
(10, '09 Inch','22,86 cm', '', 'figure_id ASC'),
(11, '10 Inch','25,40 cm', '', 'figure_id ASC'),
(12, '11 Inch','27,94 cm', '', 'figure_id ASC'),
(13, '12 Inch','30,48 cm', '', 'figure_id ASC'),
(14, '13 Inch','33,02 cm', '', 'figure_id ASC'),
(15, '14 Inch','35,56 cm', '', 'figure_id ASC'),
(16, '15 Inch','38,10 cm', '', 'figure_id ASC'),
(17, '16 Inch','40,64 cm', '', 'figure_id ASC'),
(18, '17 Inch','43,18 cm', '', 'figure_id ASC'),
(19, '18 Inch','45,72 cm', '', 'figure_id ASC'),
(20, '19 Inch','48,26 cm', '', 'figure_id ASC'),
(21, '20 Inch','50,80 cm', '', 'figure_id ASC'),
(22, '21 Inch','53,34 cm', '', 'figure_id ASC'),
(23, '22 Inch','55,88 cm', '', 'figure_id ASC'),
(24, '23 Inch','58,42 cm', '', 'figure_id ASC'),
(25, '24 Inch','60,96 cm', '', 'figure_id ASC'),
(26, '25 Inch','63,50 cm', '', 'figure_id ASC'),
(27, '26 Inch','66,04 cm', '', 'figure_id ASC'),
(28, '27 Inch','68,58 cm', '', 'figure_id ASC'),
(29, '28 Inch','71,12 cm', '', 'figure_id ASC'),
(30, '29 Inch','73,66 cm', '', 'figure_id ASC'),
(31, '30 Inch','76,20 cm', '', 'figure_id ASC'),
(32, 'bigger than 30 Inch','76,20 cm', '', 'figure_id ASC')
";
/////////////////////////////////////////////////////////////////////////////////
//befüllem von years
$inf_insertdbrow[] = DB_FIGURE_YEARS." (figure_year_id, figure_year, figure_year_description, figure_year_sorting) VALUES
(1, 'Unknown', '', 'figure_id ASC'),
(2, '1970', '', 'figure_id ASC'),
(3, '1971', '', 'figure_id ASC'),
(4, '1972', '', 'figure_id ASC'),
(5, '1973', '', 'figure_id ASC'),
(6, '1974', '', 'figure_id ASC'),
(7, '1975', '', 'figure_id ASC'),
(8, '1976', '', 'figure_id ASC'),
(9, '1977', '', 'figure_id ASC'),
(10, '1978', '', 'figure_id ASC'),
(11, '1979', '', 'figure_id ASC'),
(12, '1980', '', 'figure_id ASC'),
(13, '1981', '', 'figure_id ASC'),
(14, '1982', '', 'figure_id ASC'),
(15, '1983', '', 'figure_id ASC'),
(16, '1984', '', 'figure_id ASC'),
(17, '1985', '', 'figure_id ASC'),
(18, '1986', '', 'figure_id ASC'),
(19, '1987', '', 'figure_id ASC'),
(20, '1988', '', 'figure_id ASC'),
(21, '1989', '', 'figure_id ASC'),
(22, '1990', '', 'figure_id ASC'),
(23, '1991', '', 'figure_id ASC'),
(24, '1992', '', 'figure_id ASC'),
(25, '1993', '', 'figure_id ASC'),
(26, '1994', '', 'figure_id ASC'),
(27, '1995', '', 'figure_id ASC'),
(28, '1996', '', 'figure_id ASC'),
(29, '1997', '', 'figure_id ASC'),
(30, '1998', '', 'figure_id ASC'),
(31, '1999', '', 'figure_id ASC'),
(32, '2000', '', 'figure_id ASC'),
(33, '2001', '', 'figure_id ASC'),
(34, '2002', '', 'figure_id ASC'),
(35, '2003', '', 'figure_id ASC'),
(36, '2004', '', 'figure_id ASC'),
(37, '2005', '', 'figure_id ASC'),
(38, '2006', '', 'figure_id ASC'),
(39, '2007', '', 'figure_id ASC'),
(40, '2008', '', 'figure_id ASC'),
(41, '2009', '', 'figure_id ASC'),
(42, '2010', '', 'figure_id ASC'),
(43, '2011', '', 'figure_id ASC'),
(44, '2012', '', 'figure_id ASC'),
(45, '2013', '', 'figure_id ASC'),
(46, '2014', '', 'figure_id ASC'),
(47, '2015', '', 'figure_id ASC'),
(48, '2016', '', 'figure_id ASC'),
(49, '2017', '', 'figure_id ASC'),
(50, '2018', '', 'figure_id ASC'),
(51, '2019', '', 'figure_id ASC'),
(52, '2020', '', 'figure_id ASC'),
(53, '2021', '', 'figure_id ASC'),
(54, '2022', '', 'figure_id ASC'),
(55, '2023', '', 'figure_id ASC'),
(56, '2024', '', 'figure_id ASC'),
(57, '2025', '', 'figure_id ASC'),
(58, '2026', '', 'figure_id ASC'),
(59, '2027', '', 'figure_id ASC'),
(60, '2028', '', 'figure_id ASC'),
(61, '2029', '', 'figure_id ASC'),
(62, '2030', '', 'figure_id ASC')
";
/////////////////////////////////////////////////////////////////////////////////
//befüllem von Limitation
$inf_insertdbrow[] = DB_FIGURE_LIMITATIONS." (figure_limitation_id, figure_limitation_name) VALUES
(1, 'Unknown'),
(2, 'Yes'),
(3, 'No')
";
/////////////////////////////////////////////////////////////////////////////////
//befüllem von Packaging
$inf_insertdbrow[] = DB_FIGURE_PACKAGINGS." (figure_packaging_id, figure_packaging_name, figure_packaging_description, figure_packaging_sorting) VALUES
(1, 'Unknown', '', 'figure_id ASC'),
(2, 'Bag', '', 'figure_id ASC'),
(3, 'Blister Card', '', 'figure_id ASC'),
(4, 'Box', '', 'figure_id ASC'),
(5, 'Clamshell', '', 'figure_id ASC'),
(6, 'Slip Case', '', 'figure_id ASC'),
(7, 'Window Box', '', 'figure_id ASC')
";
/////////////////////////////////////////////////////////////////////////////////
//befüllem von Point of Articulation
$inf_insertdbrow[] = DB_FIGURE_POAS." (figure_poa_id, figure_poa_name, figure_poa_description, figure_poa_sorting) VALUES 
(1, 'Unknown', '', 'figure_id ASC'),
(2, '01 Point of articulation', '', 'figure_id ASC'),
(3, '02 Points of articulation', '', 'figure_id ASC'),
(4, '03 Points of articulation', '', 'figure_id ASC'),
(5, '04 Points of articulation', '', 'figure_id ASC'),
(6, '05 Points of articulation', '', 'figure_id ASC'),
(7, '06 Points of articulation', '', 'figure_id ASC'),
(8, '07 Points of articulation', '', 'figure_id ASC'),
(9, '08 Points of articulation', '', 'figure_id ASC'),
(10, '09 Points of articulation', '', 'figure_id ASC'),
(11, '10 Points of articulation', '', 'figure_id ASC'),
(12, '11 Points of articulation', '', 'figure_id ASC'),
(13, '12 Points of articulation', '', 'figure_id ASC'),
(14, '13 Points of articulation', '', 'figure_id ASC'),
(15, '14 Points of articulation', '', 'figure_id ASC'),
(16, '15 Points of articulation', '', 'figure_id ASC'),
(17, '16 Points of articulation', '', 'figure_id ASC'),
(18, '17 Points of articulation', '', 'figure_id ASC'),
(19, '18 Points of articulation', '', 'figure_id ASC'),
(20, '19 Points of articulation', '', 'figure_id ASC'),
(21, '20 Points of articulation', '', 'figure_id ASC'),
(22, '21 Points of articulation', '', 'figure_id ASC'),
(23, '22 Points of articulation', '', 'figure_id ASC'),
(24, '23 Points of articulation', '', 'figure_id ASC'),
(25, '24 Points of articulation', '', 'figure_id ASC'),
(26, '25 Points of articulation', '', 'figure_id ASC'),
(27, '26 Points of articulation', '', 'figure_id ASC'),
(28, '27 Points of articulation', '', 'figure_id ASC'),
(29, '28 Points of articulation', '', 'figure_id ASC'),
(30, '29 Points of articulation', '', 'figure_id ASC'),
(31, '30 Points of articulation', '', 'figure_id ASC'),
(32, '31 Points of articulation', '', 'figure_id ASC'),
(33, '32 Points of articulation', '', 'figure_id ASC'),
(34, '33 Points of articulation', '', 'figure_id ASC'),
(35, '34 Points of articulation', '', 'figure_id ASC'),
(36, '35 Points of articulation', '', 'figure_id ASC'),
(37, '36 Points of articulation', '', 'figure_id ASC'),
(38, '37 Points of articulation', '', 'figure_id ASC'),
(39, '38 Points of articulation', '', 'figure_id ASC'),
(40, '39 Points of articulation', '', 'figure_id ASC'),
(41, '40 Points of articulation', '', 'figure_id ASC'),
(42, '41 Points of articulation', '', 'figure_id ASC'),
(43, '42 Points of articulation', '', 'figure_id ASC'),
(44, '43 Points of articulation', '', 'figure_id ASC'),
(45, '44 Points of articulation', '', 'figure_id ASC'),
(46, '45 Points of articulation', '', 'figure_id ASC'),
(47, '46 Points of articulation', '', 'figure_id ASC'),
(48, '47 Points of articulation', '', 'figure_id ASC'),
(49, '48 Points of articulation', '', 'figure_id ASC'),
(50, '49 Points of articulation', '', 'figure_id ASC'),
(51, '50 Points of articulation', '', 'figure_id ASC'),
(52, 'more than 50 Points of articulation', '', 'figure_id ASC')
";
/////////////////////////////////////////////////////////////////////////////////
//befüllem von Cats
$inf_insertdbrow[] = DB_FIGURE_CATS." (figure_cat_id, figure_cat_name, figure_cat_description, figure_cat_sorting) VALUES 
(1, 'Other/Unknown', '', 'figure_id ASC'),
(2, '1/1 Scale', '', 'figure_id ASC'),
(3, '1/2 Scale', '', 'figure_id ASC'),
(4, '1/4 Scale', '', 'figure_id ASC'),
(5, '1/6 Scale', '', 'figure_id ASC'),
(6, 'Actionfigure', '', 'figure_id ASC'),
(7, 'Bootleg', '', 'figure_id ASC'),
(8, 'Bust', '', 'figure_id ASC'),
(9, 'Diorama', '', 'figure_id ASC'),
(10, 'Garage Kit', '', 'figure_id ASC'),
(11, 'Jumbo Kenner', '', 'figure_id ASC'),
(12, 'Maquette', '', 'figure_id ASC'),
(13, 'Merchandise', '', 'figure_id ASC'),
(14, 'Mini/Trade Figure', '', 'figure_id ASC'),
(15, 'Model Kit', '', 'figure_id ASC'),
(16, 'Plush', '', 'figure_id ASC'),
(17, 'Promotion', '', 'figure_id ASC'),
(18, 'Prop Replica', '', 'figure_id ASC'),
(19, 'PVC Statue', '', 'figure_id ASC'),
(20, 'Statue', '', 'figure_id ASC')
";
/////////////////////////////////////////////////////////////////////////////////
//befüllem von Materials
$inf_insertdbrow[] = DB_FIGURE_MATERIALS." (figure_material_id, figure_material_name, figure_material_description, figure_material_sorting) VALUES
(1, 'Unknown', '', 'figure_id ASC'),
(2, 'ABS', '', 'figure_id ASC'),
(3, 'Latex', '', 'figure_id ASC'),
(4, 'Cold Cast Porcelain', '', 'figure_id ASC'),
(5, 'Cold Cast Resin', '', 'figure_id ASC'),
(6, 'Glas', '', 'figure_id ASC'),
(7, 'Ceramic', '', 'figure_id ASC'),
(8, 'Copper', '', 'figure_id ASC'),
(9, 'Gold', '', 'figure_id ASC'),
(10, 'Metal', '', 'figure_id ASC'),
(11, 'MixMedia', '', 'figure_id ASC'),
(12, 'Pewter', '', 'figure_id ASC'),
(13, 'Polyresin', '', 'figure_id ASC'),
(14, 'Polystone', '', 'figure_id ASC'),
(15, 'PVC', '', 'figure_id ASC'),
(16, 'Resin', '', 'figure_id ASC'),
(17, 'Silver', '', 'figure_id ASC'),
(18, 'Soft Vinyl', '', 'figure_id ASC'),
(19, 'Stone', '', 'figure_id ASC'),
(20, 'Tiny Metal', '', 'figure_id ASC'),
(21, 'Vinyl', '', 'figure_id ASC')
";
/////////////////////////////////////////////////////////////////////////////////
$inf_insertdbrow[] = DB_FIGURE_MANUFACTURERS." (figure_manufacturer_id, figure_manufacturer_name, figure_manufacturer_description, figure_manufacturer_sorting) VALUES
(1, 'Unknown', '', 'figure_id ASC'),
(2, 'Alien Enterprise', '', 'figure_id ASC'),
(3, 'Anatomic Monster', '', 'figure_id ASC'),
(4, 'Aoshima / Skynet', '', 'figure_id ASC'),
(5, 'AMT / ERTL', '', 'figure_id ASC'),
(6, 'Art Storm', '', 'figure_id ASC'),
(7, 'AEF Designs', '', 'figure_id ASC'),
(8, 'Attakus', '', 'figure_id ASC'),
(9, 'Bandai', '', 'figure_id ASC'),
(10, 'Billiken', '', 'figure_id ASC'),
(11, 'Black Label Modelz', '', 'figure_id ASC'),
(12, 'Black Heart Enterprise', '', 'figure_id ASC'),
(13, 'Bonapart Models', '', 'figure_id ASC'),
(14, 'CoolProps', '', 'figure_id ASC'),
(15, 'Cinemaquette', '', 'figure_id ASC'),
(16, 'Cyper Model (Thailand Recasts Ebay)', '', 'figure_id ASC'),
(17, 'Dark Horse', '', 'figure_id ASC'),
(18, 'Dewar', '', 'figure_id ASC'),
(19, 'Diamond Select', '', 'figure_id ASC'),
(20, 'Diamond Select --> Sub  Art Asylum', '', 'figure_id ASC'),
(21, 'Dimensional Designs', '', 'figure_id ASC'),
(22, 'Distortions Studios', '', 'figure_id ASC'),
(23, 'Elfin', '', 'figure_id ASC'),
(24, 'Fewture Models', '', 'figure_id ASC'),
(25, 'Franklin Mint Alien', '', 'figure_id ASC'),
(26, 'Funko / Super 7', '', 'figure_id ASC'),
(27, 'Furyu', '', 'figure_id ASC'),
(28, 'Galoob', '', 'figure_id ASC'),
(29, 'Gentle Giant', '', 'figure_id ASC'),
(30, 'GEM Reproductions', '', 'figure_id ASC'),
(31, 'Geometric Design', '', 'figure_id ASC'),
(32, 'GF9 (GaleForce9 ) (Wizkids Horrorclix)', '', 'figure_id ASC'),
(33, 'Good Smile Company', '', 'figure_id ASC'),
(34, 'Gort Japan', '', 'figure_id ASC'),
(35, 'Grey Zon Sculpture Lab', '', 'figure_id ASC'),
(36, 'Henry Alvarez', '', 'figure_id ASC'),
(37, 'Herocross', '', 'figure_id ASC'),
(38, 'Hollywood Collectibles Group', '', 'figure_id ASC'),
(39, 'Hollywood Collectors Gallery', '', 'figure_id ASC'),
(40, 'Halcyon', '', 'figure_id ASC'),
(41, 'Hallmark', '', 'figure_id ASC'),
(42, 'Heartilysir', '', 'figure_id ASC'),
(43, 'Hasbro Toys', '', 'figure_id ASC'),
(44, 'Chinese Companies OGRM Manufacturing', '', 'figure_id ASC'),
(45, 'Hiya Toys', '', 'figure_id ASC'),
(46, 'Horizon', '', 'figure_id ASC'),
(47, 'Horrorclix (Wizkids)', '', 'figure_id ASC'),
(48, 'Hot Toys', '', 'figure_id ASC'),
(49, 'Insight Collectibles', '', 'figure_id ASC'),
(50, 'Inkworks', '', 'figure_id ASC'),
(51, 'Jayco', '', 'figure_id ASC'),
(52, 'JS Resines', '', 'figure_id ASC'),
(53, 'Kaiyodo', '', 'figure_id ASC'),
(54, 'Kenner', '', 'figure_id ASC'),
(55, 'Kobyoshi Kits', '', 'figure_id ASC'),
(56, 'Konami', '', 'figure_id ASC'),
(57, 'Kotobukiya', '', 'figure_id ASC'),
(58, 'Leading Edge Games', '', 'figure_id ASC'),
(59, 'Lil Monsters', '', 'figure_id ASC'),
(60, 'Mannetron', '', 'figure_id ASC'),
(61, 'Marmit', '', 'figure_id ASC'),
(62, 'Master Replicas', '', 'figure_id ASC'),
(63, 'McFarlane', '', 'figure_id ASC'),
(64, 'Medicom Toy', '', 'figure_id ASC'),
(65, 'Mental Mischief', '', 'figure_id ASC'),
(66, 'Model Kits', '', 'figure_id ASC'),
(67, 'Model Giant', '', 'figure_id ASC'),
(68, 'Modulart', '', 'figure_id ASC'),
(69, 'Monsters.net', '', 'figure_id ASC'),
(70, 'Morbid Model', '', 'figure_id ASC'),
(71, 'Morpheus International', '', 'figure_id ASC'),
(72, 'Multiverse Studio', '', 'figure_id ASC'),
(73, 'MPC / Round2 Models', '', 'figure_id ASC'),
(74, 'Narin Naward Productions Modelmagic5', '', 'figure_id ASC'),
(75, 'Neca', '', 'figure_id ASC'),
(76, 'Nexus', '', 'figure_id ASC'),
(77, 'Ogawa Studio', '', 'figure_id ASC'),
(78, 'Oyama', '', 'figure_id ASC'),
(79, 'OZ Shop', '', 'figure_id ASC'),
(80, 'Palisades', '', 'figure_id ASC'),
(81, 'Polar Lights', '', 'figure_id ASC'),
(82, 'Prodos Games', '', 'figure_id ASC'),
(83, 'Psycho Monsterz', '', 'figure_id ASC'),
(84, 'Real', '', 'figure_id ASC'),
(85, 'Resin d etre', '', 'figure_id ASC'),
(86, 'Revengemonst (Recasts)', '', 'figure_id ASC'),
(87, 'Roswell Japan', '', 'figure_id ASC'),
(88, 'Seahorse Models', '', 'figure_id ASC'),
(89, 'Sega', '', 'figure_id ASC'),
(90, 'Sideshow Collectibles', '', 'figure_id ASC'),
(91, 'Sky W', '', 'figure_id ASC'),
(92, 'Sota', '', 'figure_id ASC'),
(93, 'Spacecraft Creation Models', '', 'figure_id ASC'),
(94, 'Super 7 / Secret Base', '', 'figure_id ASC'),
(95, 'Squareenix', '', 'figure_id ASC'),
(96, 'Star Pics', '', 'figure_id ASC'),
(97, 'Takara', '', 'figure_id ASC'),
(98, 'Takeya Takayuki', '', 'figure_id ASC'),
(99, 'The Flying Gung Brothers', '', 'figure_id ASC'),
(100, 'Time Slip', '', 'figure_id ASC'),
(101, 'Titan Books', '', 'figure_id ASC'),
(102, 'Titan Merchandise', '', 'figure_id ASC'),
(103, 'ThreeB', '', 'figure_id ASC'),
(104, 'Tsukuda Hobby', '', 'figure_id ASC'),
(105, 'TOPPS', '', 'figure_id ASC'),
(106, 'Upper Deck', '', 'figure_id ASC'),
(107, 'Vision2 Art Media Store', '', 'figure_id ASC'),
(108, 'Voodoo Babe', '', 'figure_id ASC'),
(109, 'Wizart Studio', '', 'figure_id ASC'),
(110, 'Wizkids', '', 'figure_id ASC'),
(111, 'X-Plus', '', 'figure_id ASC'),
(112, 'Yellow Pearl', '', 'figure_id ASC'),
(113, '3dwizzard (also Wizart Studio)', '', 'figure_id ASC'),
(114, '3rd Eye Design', '', 'figure_id ASC')
";
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
// so lange betaphase erstmal auskommentieren weil sond die ganzen submittings weg sind und alle mühesam per hand neu eingereicht werden müssen
//$inf_deldbrow[] = DB_SUBMISSIONS." WHERE submit_type='f'";
