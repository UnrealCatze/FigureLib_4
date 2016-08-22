<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: settings_figurelib.php based on settings_news.php
| Author: Starefossen
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
pageAccess("FI");
require_once THEMES. "templates/admin_header.php";

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

$locale = fusion_get_locale();

// SETTINGS HOLEN
$fil_settings = get_settings("figurelib");

global $defender;

// Handle posted Form
if (isset($_POST['savesettings'])) {
	$error = false;
	
	// Check posted Fields and save to Array
	$inputArray = [
		"figure_notification"         => form_sanitizer($_POST['figure_notification'],         1,    "figure_notification"),
		"figure_notification_subject" => form_sanitizer($_POST['figure_notification_subject'], "",   "figure_notification_subject"),
		"figure_notification_message" => form_sanitizer($_POST['figure_notification_message'], "",   "figure_notification_message"),
		"figure_per_page"             => form_sanitizer($_POST['figure_per_page'],             0,    "figure_per_page"),
		"figure_per_line"             => form_sanitizer($_POST['figure_per_line'],             0,    "figure_per_line"),
		"figure_image_upload_count"   => form_sanitizer($_POST['figure_image_upload_count'],   10,   "figure_image_upload_count"),	
		"figure_thumb_w"              => form_sanitizer($_POST['figure_thumb_w'],              300,  "figure_thumb_w"),
		"figure_thumb_h"              => form_sanitizer($_POST['figure_thumb_h'],              150,  "figure_thumb_h"),
		"figure_thumb_ratio"          => form_sanitizer($_POST['figure_thumb_ratio'],          0,    "figure_thumb_ratio"),
		"figure_thumb2_w"             => form_sanitizer($_POST['figure_thumb2_w'],             40,  "figure_thumb2_w"),
		"figure_thumb2_h"             => form_sanitizer($_POST['figure_thumb2_h'],             40,  "figure_thumb2_h"),
		"figure_thumb2_ratio"         => form_sanitizer($_POST['figure_thumb2_ratio'],         0,    "figure_thumb2_ratio"),				
		"figure_thumb_cat_w"          => form_sanitizer($_POST['figure_thumb_cat_w'],          100,  "figure_thumb_cat_w"),
		"figure_thumb_cat_h"          => form_sanitizer($_POST['figure_thumb_cat_h'],          100,  "figure_thumb_cat_h"),
		"figure_thumb_cat_ratio"      => form_sanitizer($_POST['figure_thumb_cat_ratio'],      0,    "figure_thumb_cat_ratio"),
		"figure_thumb_man_w"          => form_sanitizer($_POST['figure_thumb_man_w'],          100,  "figure_thumb_man_w"),
		"figure_thumb_man_h"          => form_sanitizer($_POST['figure_thumb_man_h'],          100,  "figure_thumb_man_h"),
		"figure_thumb_man_ratio"      => form_sanitizer($_POST['figure_thumb_man_ratio'],      0,    "figure_thumb_man_ratio"),		
		"figure_photo_w"              => form_sanitizer($_POST['figure_photo_w'],              400,  "figure_photo_w"),
		"figure_photo_h"              => form_sanitizer($_POST['figure_photo_h'],              300,  "figure_photo_h"),
		"figure_photo_max_w"          => form_sanitizer($_POST['figure_photo_max_w'],          1800, "figure_photo_max_w"),
		"figure_photo_max_h"          => form_sanitizer($_POST['figure_photo_max_h'],          1600, "figure_photo_max_h"),
		"figure_photo_max_b"          => form_sanitizer($_POST['calc_b'], 150, "calc_b") * form_sanitizer($_POST['calc_c'], 100000, "calc_c"),				
		"figure_photo_cat_max_b"      => form_sanitizer($_POST['calc_b_cat'], 150, "calc_b_cat") * form_sanitizer($_POST['calc_c_cat'], 100000, "calc_c_cat"),
		"figure_photo_man_max_b"      => form_sanitizer($_POST['calc_b_man'], 150, "calc_b_man") * form_sanitizer($_POST['calc_c_man'], 100000, "calc_c_man"),		
		"figure_allow_comments"       => isset($_POST['figure_allow_comments']) ? 1 : 0,
		"figure_allow_ratings"        => isset($_POST['figure_allow_ratings'])  ? 1 : 0,
		"figure_display"              => isset($_POST['figure_display'])        ? 1 : 0,
		"figure_submit"               => isset($_POST['figure_submit'])         ? 1 : 0,
		"figure_related"              => isset($_POST['figure_related'])        ? 1 : 0,
		"figure_social_sharing"       => isset($_POST['figure_social_sharing']) ? 1 : 0
	];
	
	// Check Notifications
	if ($inputArray['figure_notification'] == "2" || $inputArray['figure_notification'] == "3") {
		if (!$inputArray['figure_notification_subject']) {
			$defender -> setInputError("figure_notification_subject");
			$defender -> setErrorText("figure_notification_subject", $locale['notification-101']);
			$error = true;
		}
		if (!$inputArray['figure_notification_message']) {
			$defender -> setInputError("figure_notification_message");
			$defender -> setErrorText("figure_notification_message", $locale['notification-102']);
			$error = true;
		}
		if ($error) {
			defender::stop();
		}
	}
		
	// If there are no Errors, save it
	if (defender::safe()) {
		foreach ($inputArray as $settings_name => $settings_value) {
			$inputSettings = array(
				"settings_name" => $settings_name, 
				"settings_value" => $settings_value, 
				"settings_inf" => "figurelib",
			);
			dbquery_insert(DB_SETTINGS_INF, $inputSettings, "update", array("primary_key" => "settings_name"));
		}
		// Display Message
		addNotice("success", $locale['900']);
		
	// Display Error Message
	} else {
		addNotice('danger', $locale['901'], FUSION_SELF, false);
	}
	
	// Get all Form Informations to Settings Array, so we don't lost all Values if Form failed.
	$fil_settings = $inputArray;
}

// Open Form
opentable($locale['figure_settings']);
echo openform('settingsform', 'post', FUSION_REQUEST, array('class' => "m-t-20"));


// Display Form
echo "<div class='row'><div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
openside("General Settings");
	echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>\n";
	
	// Set Options for Selectlists
	$thumb_opts = [
		'0' => $locale['admin_figurelib_settings.php_001'], 
		'1' => $locale['admin_figurelib_settings.php_002']
	];
	$calc_opts = [
		'1' => 'Bytes (bytes)', 
		'1000' => 'KB (Kilobytes)', 
		'1000000' => 'MB (Megabytes)'
	];

	// Calculate
	$calc_c = calculate_byte($asettings['figure_photo_max_b']);
	$calc_b = $asettings['figure_photo_max_b'] / $calc_c;	
	$calc_c_cat = calculate_byte($asettings['figure_photo_cat_max_b']);   
	$calc_b_cat = $asettings['figure_photo_cat_max_b'] / $calc_c_cat;	
	$calc_c_man = calculate_byte($asettings['figure_photo_man_max_b']);
	$calc_b_man = $asettings['figure_photo_man_max_b'] / $calc_c_man;

	// ['figure_334'] = "Figures per page:";
	// ['figure_361'] = "Only values 1-500 allowed!";
	echo form_text('figure_per_page', $locale['figure_334'], $fil_settings['figure_per_page'], array(
		'inline' => 1,
		'required' => 1,
		'error_text' => $locale['figure_361'],
		'type' => 'number',
		'min' => 1,
		'max' => 500,
		'width' => '250px'
	));

	// ['figure_357'] = "Figures per line:";
	// ['figure_362'] = "Only values 1-10 allowed!";
	echo form_text('figure_per_line', $locale['figure_357'], $fil_settings['figure_per_line'], array(
		'inline' => 1,
		'required' => 1,
		'error_text' => $locale['figure_362'],
		'number' => 1,
		'min' => 1,
		'max' => 10,
		'type' => 'number',
		'width' => '250px'
	));	

	// ['figure_335'] = "Allow users to submit figures:";
	// ALS CHECKBOX
	echo form_checkbox("figure_submit", $locale['figure_335'], $fil_settings['figure_submit']);
	/*	
	// ALS DROPDOWN 	
		echo form_select("figure_submit", $locale['figure_335'], $fil_settings['figure_submit'], array(
			"inline" => TRUE, 
			"options" => array($locale['disable'], $locale['enable'])
		));
	*/
	
	// ['figure_344'] = "Allow Social Sharing:";	
	// ALS CHECKBOX
	echo form_checkbox('figure_social_sharing', $locale['figure_344'], $fil_settings['figure_social_sharing']);
	/*
	// ALS DROPDOWN
		echo form_select("figure_social_sharing", $locale['figure_344'], $fil_settings['figure_social_sharing'], array(
			"inline" => TRUE, 
			"options" => array($locale['disable'], $locale['enable'])
		));
	*/

echo "</div>\n";		
echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>\n";	
		
	// ['figure_348'] = "Show related figures:";
	// ALS CHECKBOX
	echo form_checkbox('figure_related', $locale['figure_348'], $fil_settings['figure_related']);
	/*	
	// ALS DROPDOWN
	echo form_select("figure_related", $locale['figure_348'], $fil_settings['figure_related'], array(
			"inline" => TRUE, 
			"options" => array($locale['disable'], $locale['enable'])
		));
	*/	

	// ['figure_363'] = "Allow Comments:";
	// ALS CHECKBOX
	echo form_checkbox('figure_allow_comments', $locale['figure_363'], $fil_settings['figure_allow_comments']);
		
	// ['figure_364'] = "Allow Ratings:";
	// ALS CHECKBOX
	echo form_checkbox('figure_allow_ratings', $locale['figure_364'], $fil_settings['figure_allow_ratings']);

	// ['figure_339'] = "Gallery Mode on";
	echo form_checkbox('figure_display', $locale['figure_339'], $fil_settings['figure_display']);
echo "</div>\n";
closeside();
echo "</div>\n";	
// General settings END ##################################################################################


// #######################################################################################################
// Display Figure Image Settings START
echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";	
	openside("Figure Image Settings");

	//$locale['figure_365'] = "Image upload count per figure:";
	echo form_text('figure_image_upload_count', $locale['figure_365'], $fil_settings['figure_image_upload_count'], array(
		'inline' => 1,
		'required' => 1,
		'number' => 1,
		'min' => 1,
		'max' => 100,
		'type' => 'number',
		'width' => '250px'
	));	
	
	// $locale['admin_figurelib_settings.php_009'] = "Thumb2 size:";
// $locale['admin_figurelib_settings.php_006'] = "Width x Height";
echo "
<div class='row'>
	<div class='col-xs-12 col-sm-3'>
		<label for='figure_thumb2_w'>".$locale['admin_figurelib_settings.php_009']."</label>
	</div>
	<div class='col-xs-12 col-sm-9'>
	".form_text(
	'figure_thumb2_w', '', $fil_settings['figure_thumb2_w'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<i class='entypo icancel pull-left m-r-10 m-l-0 m-t-10'></i>
	".form_text('figure_thumb2_h', '', $fil_settings['figure_thumb2_h'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<small class='m-l-10 mid-opacity text-uppercase pull-left m-t-10'>( ".$locale['admin_figurelib_settings.php_006']." )</small>
	</div>
</div>";

// $locale['admin_figurelib_settings.php_003'] = "Thumb size:";
// $locale['admin_figurelib_settings.php_006'] = "Width x Height";
echo "
<div class='row'>
	<div class='col-xs-12 col-sm-3'>
		<label for='figure_thumb_w'>".$locale['admin_figurelib_settings.php_003']."</label>
	</div>
	<div class='col-xs-12 col-sm-9'>
	".form_text(
	'figure_thumb_w', '', $fil_settings['figure_thumb_w'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<i class='entypo icancel pull-left m-r-10 m-l-0 m-t-10'></i>
	".form_text('figure_thumb_h', '', $fil_settings['figure_thumb_h'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<small class='m-l-10 mid-opacity text-uppercase pull-left m-t-10'>( ".$locale['admin_figurelib_settings.php_006']." )</small>
	</div>
</div>";

// $locale['admin_figurelib_settings.php_004'] = "Photo size:";
// $locale['admin_figurelib_settings.php_006'] = "Width x Height";
echo "
<div class='row'>
	<div class='col-xs-12 col-sm-3'>
		<label for='figure_photo_w'>".$locale['admin_figurelib_settings.php_004']."</label>
	</div>
	<div class='col-xs-12 col-sm-9'>
	".form_text('figure_photo_w', '', $fil_settings['figure_photo_w'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<i class='entypo icancel pull-left m-r-10 m-l-0 m-t-10'></i>
	".form_text('figure_photo_h', '', $fil_settings['figure_photo_h'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<small class='m-l-10 mid-opacity text-uppercase pull-left m-t-10'>( ".$locale['admin_figurelib_settings.php_006']." )</small>
	</div>
</div>";

// $locale['admin_figurelib_settings.php_005'] = "Maximum photo size:";
// $locale['admin_figurelib_settings.php_006'] = "Width x Height";
echo "
<div class='row'>
	<div class='col-xs-12 col-sm-3'>
		<label for='figure_photo_max_w'>".$locale['admin_figurelib_settings.php_005']."</label>
	</div>
	<div class='col-xs-12 col-sm-9'>
	".form_text('figure_photo_max_w', '', $fil_settings['figure_photo_max_w'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<i class='entypo icancel pull-left m-r-10 m-l-0 m-t-10'></i>
	".form_text('figure_photo_max_h', '', $fil_settings['figure_photo_max_h'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<small class='m-l-10 mid-opacity text-uppercase pull-left m-t-10'>( ".$locale['admin_figurelib_settings.php_006']." )</small>
	</div>
</div>";

// $locale['admin_figurelib_settings.php_007'] = "Maximum file size (bytes):";
echo "
<div class='row'>
	<div class='col-xs-12 col-sm-3'>
		<label for='calc_b'>".$locale['admin_figurelib_settings.php_007']."</label>
	</div>
	<div class='col-xs-12 col-sm-9'>
	".form_text('calc_b', '', $calc_b, array(
		'required' => 1, 'number' => 1, 'error_text' => $locale['error_rate'], 'width' => '100px', 'max_length' => 4,
		'class' => 'pull-left m-r-10'
	))."
	".form_select('calc_c', '', $calc_c, array(
		'options' => $calc_opts, 'placeholder' => $locale['choose'], 'class' => 'pull-left', 'width' => '180px'
	))."
	</div>
</div>";	
	
// ['admin_figurelib_settings.php_008'] = "Thumb ratio:";	
echo form_select('figure_thumb_ratio', $locale['admin_figurelib_settings.php_008'], $fil_settings['figure_thumb_ratio'], array("options" => $thumb_opts));	

	echo "</div>\n";	

// Display Figure Image Settings END
	closeside();
// #######################################################################################################


// #######################################################################################################
// Display Category Image Settings START
echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
	openside("Category Image Settings");

// $locale['admin_figurelib_settings.php_011'] = "Thumb Category size:";	
// $locale['admin_figurelib_settings.php_006'] = "Width x Height";
echo "
<div class='row'>
	<div class='col-xs-12 col-sm-3'>
		<label for='figure_thumb_cat_w'>".$locale['admin_figurelib_settings.php_011']."</label>
	</div>
	<div class='col-xs-12 col-sm-9'>
	".form_text(
	'figure_thumb_cat_w', '', $fil_settings['figure_thumb_cat_w'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<i class='entypo icancel pull-left m-r-10 m-l-0 m-t-10'></i>
	".form_text('figure_thumb_cat_h', '', $fil_settings['figure_thumb_cat_h'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<small class='m-l-10 mid-opacity text-uppercase pull-left m-t-10'>( ".$locale['admin_figurelib_settings.php_006']." )</small>
	</div>
</div>";

// $locale['admin_figurelib_settings.php_013'] = "Maximum photo Category size:";
// $locale['admin_figurelib_settings.php_006'] = "Width x Height";
echo "
<div class='row'>
	<div class='col-xs-12 col-sm-3'>
		<label for='figure_photo_cat_max_w'>".$locale['admin_figurelib_settings.php_013']."</label>
	</div>
	<div class='col-xs-12 col-sm-9'>
	".form_text('figure_photo_cat_max_w', '', $fil_settings['figure_photo_cat_max_w'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<i class='entypo icancel pull-left m-r-10 m-l-0 m-t-10'></i>
	".form_text('figure_photo_cat_max_h', '', $fil_settings['figure_photo_cat_max_h'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<small class='m-l-10 mid-opacity text-uppercase pull-left m-t-10'>( ".$locale['admin_figurelib_settings.php_006']." )</small>
	</div>
</div>";

//$locale['admin_figurelib_settings.php_007'] = "Maximum file size (bytes):";	
echo "
<div class='row'>
	<div class='col-xs-12 col-sm-3'>
		<label for='calc_b_cat'>".$locale['admin_figurelib_settings.php_007']."</label>
	</div>
	<div class='col-xs-12 col-sm-9'>
	".form_text('calc_b_cat', '', $calc_b_cat, array(
		'required' => 1, 'number' => 1, 'error_text' => $locale['error_rate'], 'width' => '100px', 'max_length' => 4,
		'class' => 'pull-left m-r-10'
	))."
	".form_select('calc_c_cat', '', $calc_c_cat, array(
		'options' => $calc_opts, 'placeholder' => $locale['choose'], 'class' => 'pull-left', 'width' => '180px'
	))."
	</div>
</div>
";

	// ['admin_figurelib_settings.php_008'] = "Thumb ratio:";	--> hier für Category
	echo form_select('figure_thumb_cat_ratio', $locale['admin_figurelib_settings.php_008'], $fil_settings['figure_thumb_cat_ratio'], array("options" => $thumb_opts));
	
echo "</div>\n";	

// Display Category Image Settings END
	closeside();
// #######################################################################################################


// #######################################################################################################
// Display Manufacturer Image Settings START
echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";	
	openside("Manufacturer Image Settings");

// $locale['admin_figurelib_settings.php_015'] = "Thumb Manufacturer size:";	
// $locale['admin_figurelib_settings.php_006'] = "Width x Height";
echo "
<div class='row'>
	<div class='col-xs-12 col-sm-3'>
		<label for='figure_thumb_man_w'>".$locale['admin_figurelib_settings.php_015']."</label>
	</div>
	<div class='col-xs-12 col-sm-9'>
	".form_text(
	'figure_thumb_man_w', '', $fil_settings['figure_thumb_man_w'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<i class='entypo icancel pull-left m-r-10 m-l-0 m-t-10'></i>
	".form_text('figure_thumb_man_h', '', $fil_settings['figure_thumb_man_h'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<small class='m-l-10 mid-opacity text-uppercase pull-left m-t-10'>( ".$locale['admin_figurelib_settings.php_006']." )</small>
	</div>
</div>";

// $locale['admin_figurelib_settings.php_014'] = "Maximum photo Manufacturer size:";
// $locale['admin_figurelib_settings.php_006'] = "Width x Height";
echo "
<div class='row'>
	<div class='col-xs-12 col-sm-3'>
		<label for='figure_photo_man_max_w'>".$locale['admin_figurelib_settings.php_014']."</label>
	</div>
	<div class='col-xs-12 col-sm-9'>
	".form_text('figure_photo_man_max_w', '', $fil_settings['figure_photo_man_max_w'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<i class='entypo icancel pull-left m-r-10 m-l-0 m-t-10'></i>
	".form_text('figure_photo_man_max_h', '', $fil_settings['figure_photo_man_max_h'], array(
		'class' => 'pull-left', 'max_length' => 4, 'number' => 1, 'width' => '150px'
	))."
	<small class='m-l-10 mid-opacity text-uppercase pull-left m-t-10'>( ".$locale['admin_figurelib_settings.php_006']." )</small>
	</div>
</div>";

//$locale['admin_figurelib_settings.php_007'] = "Maximum file size (bytes):";	
echo "
<div class='row'>
	<div class='col-xs-12 col-sm-3'>
		<label for='calc_b_man'>".$locale['admin_figurelib_settings.php_007']."</label>
	</div>
	<div class='col-xs-12 col-sm-9'>
	".form_text('calc_b_man', '', $calc_b_man, array(
		'required' => 1, 'number' => 1, 'error_text' => $locale['error_rate'], 'width' => '100px', 'max_length' => 4,
		'class' => 'pull-left m-r-10'
	))."
	".form_select('calc_c_man', '', $calc_c_man, array(
		'options' => $calc_opts, 'placeholder' => $locale['choose'], 'class' => 'pull-left', 'width' => '180px'
	))."
	</div>
</div>
";

	// ['admin_figurelib_settings.php_008'] = "Thumb ratio:";	-->  hier für Manufacturer
	echo form_select('figure_thumb_man_ratio', $locale['admin_figurelib_settings.php_008'], $fil_settings['figure_thumb_man_ratio'], array("options" => $thumb_opts));
	
echo "</div>\n";	

// Display Manufacturer Image Settings END
	closeside();
// #######################################################################################################


// Display Notification Settings START ###################################################################
echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
	openside("Notification Settings");
	$notificationOptions = [
		"1" => $locale['notification-103'],
		"2" => $locale['notification-104'],
		"3" => $locale['notification-105']
	];
	
	// Notification
	echo form_select("figure_notification", $locale['notification-107'], $fil_settings['figure_notification'], [
		"options" => $notificationOptions,
		"placeholder" => $locale['notification-106'],
		"class" => "pull-left",
		"width" => "180px",
		"required" => true
	]); 

	// Notification Subject
	echo form_text("figure_notification_subject", $locale['notification-108'], $fil_settings['figure_notification_subject'], [
		"required" => false,
		"width"    => "50%"
	]);
	
	// Notification Message
	echo form_textarea("figure_notification_message", $locale['notification-109'], $fil_settings['figure_notification_message'], [
		"required"  => false,
		"type"      => "bbcode",
		"form_name" => "settingsform",
		"width"     => "80%",
		"height"    => "140px",
		"tip"       => $locale['notification-110']
	]);
	
// Display Notification Settings END
	closeside();
// #######################################################################################################
echo "</div>\n";

// Save Settings Button
echo form_button("savesettings", $locale['figure_345'], $locale['figure_345'], ["class" => "btn-success"]);

// Closeform
echo closeform();
closetable();

// Calculate
function calculate_byte($total_bit) {
	$calc_opts = array(1 => 'Bytes (bytes)', 1000 => 'KB (Kilobytes)', 1000000 => 'MB (Megabytes)');
	foreach ($calc_opts as $byte => $val) {
		if ($total_bit/$byte <= 999) {
			return (int)$byte;
		}
	}
	return 1000000;
}

