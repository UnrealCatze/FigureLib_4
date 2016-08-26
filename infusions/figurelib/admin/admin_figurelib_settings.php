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
		"figure_notification"         		  => form_sanitizer($_POST['figure_notification'],         		 1,     "figure_notification"),
		"figure_notification_subject" 		  => form_sanitizer($_POST['figure_notification_subject'], 		"",     "figure_notification_subject"),
		"figure_notification_message" 		  => form_sanitizer($_POST['figure_notification_message'], 		"",     "figure_notification_message"),			
		"figure_allow_submit_videos"  		  => form_sanitizer($_POST['figure_allow_submit_videos'],         0,    "figure_allow_submit_videos"), 		
		"figure_show_images_global"      	  => form_sanitizer($_POST['figure_show_images_global'],          0,    "figure_show_images_global"),
		"figure_show_data_global"        	  => form_sanitizer($_POST['figure_show_data_global'],            0,    "figure_show_data_global"),
		"figure_show_videos_global"      	  => form_sanitizer($_POST['figure_show_videos_global'],          0,    "figure_show_videos_global"),
		
		"figure_show_affiliates_complete_global" => form_sanitizer($_POST['figure_show_affiliates_complete_global'],  0,    "figure_show_affiliates_complete_global"),
		"figure_show_affiliates_other_global"    => form_sanitizer($_POST['figure_show_affiliates_other_global'],     0,    "figure_show_affiliates_other_global"),
		
		"figure_show_amazon_global"      	  => form_sanitizer($_POST['figure_show_amazon_global'],          0,    "figure_show_amazon_global"),
		"figure_show_ebay_global"        	  => form_sanitizer($_POST['figure_show_ebay_global'],            0,    "figure_show_ebay_global"),
		"figure_show_collection_global"       => form_sanitizer($_POST['figure_show_collection_global'],      0,    "figure_show_collection_global"),
		"figure_show_related_global"          => form_sanitizer($_POST['figure_show_related_global'],         0,    "figure_show_related_global"),
		"figure_show_comments_global"   	  => form_sanitizer($_POST['figure_show_comments_global'],        0,    "figure_show_comments_global"),
		"figure_show_ratings_global"    	  => form_sanitizer($_POST['figure_show_ratings_global'],         0,    "figure_show_ratings_global"),
		"figure_show_social_sharing_global"   => form_sanitizer($_POST['figure_show_social_sharing_global'],  0,    "figure_show_social_sharing_global"),			
		"figure_display"              		  => form_sanitizer($_POST['figure_display'],                     0,    "figure_display"),
		"figure_submit"                       => form_sanitizer($_POST['figure_submit'],                      0,    "figure_submit"),
		
		
		"figure_per_page"             => form_sanitizer($_POST['figure_per_page'],             0,    "figure_per_page"),
		"figure_per_line"             => form_sanitizer($_POST['figure_per_line'],             0,    "figure_per_line"),
		"figure_image_upload_count"   => form_sanitizer($_POST['figure_image_upload_count'],   10,   "figure_image_upload_count"),	
		"figure_thumb_w"              => form_sanitizer($_POST['figure_thumb_w'],              300,  "figure_thumb_w"),
		"figure_thumb_h"              => form_sanitizer($_POST['figure_thumb_h'],              150,  "figure_thumb_h"),
		"figure_thumb2_w"             => form_sanitizer($_POST['figure_thumb2_w'],             40,   "figure_thumb2_w"),
		"figure_thumb2_h"             => form_sanitizer($_POST['figure_thumb2_h'],             40,   "figure_thumb2_h"),
		"figure_thumb_cat_w"          => form_sanitizer($_POST['figure_thumb_cat_w'],          100,  "figure_thumb_cat_w"),
		"figure_thumb_cat_h"          => form_sanitizer($_POST['figure_thumb_cat_h'],          100,  "figure_thumb_cat_h"),
		"figure_thumb_man_w"          => form_sanitizer($_POST['figure_thumb_man_w'],          100,  "figure_thumb_man_w"),
		"figure_thumb_man_h"          => form_sanitizer($_POST['figure_thumb_man_h'],          100,  "figure_thumb_man_h"),
		"figure_photo_max_w"          => form_sanitizer($_POST['figure_photo_max_w'],          1800, "figure_photo_max_w"),
		"figure_photo_max_h"          => form_sanitizer($_POST['figure_photo_max_h'],          1600, "figure_photo_max_h"),
		"figure_photo_cat_max_w"      => form_sanitizer($_POST['figure_photo_cat_max_w'],      1800, "figure_photo_cat_max_w"),
		"figure_photo_cat_max_h"      => form_sanitizer($_POST['figure_photo_cat_max_h'],      1600, "figure_photo_cat_max_h"),
		"figure_photo_man_max_w"      => form_sanitizer($_POST['figure_photo_man_max_w'],      1800, "figure_photo_man_max_w"),
		"figure_photo_man_max_h"      => form_sanitizer($_POST['figure_photo_man_max_h'],      1600, "figure_photo_man_max_h"),
		
		"figure_photo_max_b"          => form_sanitizer($_POST['calc_b'], 150, "calc_b") * form_sanitizer($_POST['calc_c'], 100000, "calc_c"),				
		"figure_photo_cat_max_b"      => form_sanitizer($_POST['calc_b_cat'], 150, "calc_b_cat") * form_sanitizer($_POST['calc_c_cat'], 100000, "calc_c_cat"),
		"figure_photo_man_max_b"      => form_sanitizer($_POST['calc_b_man'], 150, "calc_b_man") * form_sanitizer($_POST['calc_c_man'], 100000, "calc_c_man")			
		
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

#################### General Settings ########################################################

		/*-----------------------------------------------------------------------
		|figure_per_page 				|  figure_submit  	                 	| 
		|figure_per_line				|  figure_allow_submit_videos 		 	| 
		|figure_display 				|   									| 
		------------------------------------------------------------------------*/

openside("<div class='text-bold text-uppercase'>".$locale['figure_375']."</div>");

echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";	
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
				
				// ['figure_339'] = "Gallery Mode on/off";
					// ALS CHECKBOX
						//echo form_checkbox('figure_display', $locale['figure_339'], $fil_settings['figure_display']);					
					// ALS DROPDOWN
						echo form_select("figure_display", $locale['figure_339'], $fil_settings['figure_display'], array(
							"inline" => TRUE, 
							"options" => array($locale['disable'], $locale['enable'])
						));
	
	echo "</div>\n";
	echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>\n";
			
				// ['figure_335'] = "Allow users to submit figures:";
					// ALS CHECKBOX
						//echo form_checkbox("figure_submit", $locale['figure_335'], $fil_settings['figure_submit']);		
					// ALS DROPDOWN 	
						echo form_select("figure_submit", $locale['figure_335'], $fil_settings['figure_submit'], array(
							"inline" => TRUE, 
							"options" => array($locale['disable'], $locale['enable'])
						));
					
				// $locale['figure_366'] = "Allow submit videos:"; --> figure_allow_submit_videos
					// ALS CHECKBOX
						//echo form_checkbox("figure_allow_submit_videos", $locale['figure_366'], $fil_settings['figure_allow_submit_videos']);
					// ALS DROPDOWN 	
						echo form_select("figure_allow_submit_videos", $locale['figure_366'], $fil_settings['figure_allow_submit_videos'], array(
								"inline" => TRUE, 
								"options" => array($locale['disable'], $locale['enable'])
						));												
				
	echo "</div>\n";
	echo "</div>\n";

closeside();

	echo "</div>\n";

#################### General Settings Module on/off ########################################################

		/*---------------------------------------------------------------------------------------
		| figure_show_images_global				| 	----------------------------------------	|
		| figure_show_data_global				| 	|figure_show_affiliates_complete_global|	|
		| figure_show_videos_global				|	----------------------------------------	|
		| figure_show_collection_global			|												|
		| figure_show_related					|	figure_show_affiliates_other_global			|
		| figure_show_comments_global			|	figure_show_amazon_global					|	
		| figure_show_ratings_global			|	figure_show_ebay_global						|
		| figure_show_social_sharing_global		|												|
		-----------------------------------------------------------------------------------------
		| HINWEIS: WENN CHECKBOX DANN --> => isset($_POST['figure_display'])  ? 1 : 0, 	  		|
		---------------------------------------------------------------------------------------*/

	echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";				

openside("<div class='text-bold text-uppercase'>".$locale['figure_376']."</div>");

	echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>\n";

		/*--------------------------------------------------------------------------------------
		| figure_show_images_global				| 											  	|
		| figure_show_data_global				| 											  	|
		| figure_show_videos_global				| 											  	|
		| figure_show_collection_global			| 											  	|
		| figure_show_related					|											  	|
		| figure_show_comments_global			|											  	|	
		| figure_show_ratings_global			|												|
		| figure_show_social_sharing_global		|												|
		---------------------------------------------------------------------------------------*/

				// ['figure_371'] = "Images:"; --> figure_show_images_global
						// ALS CHECKBOX
							//echo form_checkbox('figure_show_images_global', $locale['figure_371'], $fil_settings['figure_show_images_global']);	
						// ALS DROPDOWN
							echo form_select("figure_show_images_global", $locale['figure_371'], $fil_settings['figure_show_images_global'], array(
								"inline" => TRUE, 
								"options" => array($locale['disable'], $locale['enable'])
							));

				// ['figure_372'] = "Data:"; --> figure_show_data_global
						// ALS CHECKBOX
							//echo form_checkbox('figure_show_data_global', $locale['figure_372'], $fil_settings['figure_show_data_global']);	
						// ALS DROPDOWN
							echo form_select("figure_show_data_global", $locale['figure_372'], $fil_settings['figure_show_data_global'], array(
								"inline" => TRUE, 
								"options" => array($locale['disable'], $locale['enable'])
							));
							
				// ['figure_367'] = "Videos:"; --> figure_show_videos_global
						// ALS CHECKBOX
							//echo form_checkbox('figure_show_videos_global', $locale['figure_367'], $fil_settings['figure_show_videos_global']);		
						// ALS DROPDOWN
							echo form_select("figure_show_videos_global", $locale['figure_367'], $fil_settings['figure_show_videos_global'], array(
								"inline" => TRUE, 
								"options" => array($locale['disable'], $locale['enable'])
							));	
							
				// ['figure_373'] = "Collection:"; --> figure_show_collection_global
						// ALS CHECKBOX
							//echo form_checkbox('figure_show_collection_global', $locale['figure_373'], $fil_settings['figure_show_related_global']);	
						// ALS DROPDOWN
							echo form_select("figure_show_collection_global", $locale['figure_373'], $fil_settings['figure_show_collection_global'], array(
								"inline" => TRUE, 
								"options" => array($locale['disable'], $locale['enable'])
							));
				
				// ['figure_348'] = "Related:"; --> figure_releated show_related
						// ALS CHECKBOX
							//echo form_checkbox('figure_show_related_global', $locale['figure_348'], $fil_settings['figure_show_related_global']);	
						// ALS DROPDOWN
							echo form_select("figure_show_related_global", $locale['figure_348'], $fil_settings['figure_show_related_global'], array(
								"inline" => TRUE, 
								"options" => array($locale['disable'], $locale['enable'])
							));

				// ['figure_363'] = "Comments:"; --> figure_show_comments_global
						// ALS CHECKBOX
							//echo form_checkbox('figure_show_comments_global', $locale['figure_363'], $fil_settings['figure_show_comments_global']);
						// ALS DROPDOWN
							echo form_select("figure_show_comments_global", $locale['figure_363'], $fil_settings['figure_show_comments_global'], array(
								"inline" => TRUE, 
								"options" => array($locale['disable'], $locale['enable'])
							));
				
				// ['figure_364'] = "Ratings:"; --> figure_show_ratings_global
						// ALS CHECKBOX 
							//echo form_checkbox('figure_show_ratings_global', $locale['figure_364'], $fil_settings['figure_show_ratings_global']);
						// ALS DROPDOWN
							echo form_select("figure_show_ratings_global", $locale['figure_364'], $fil_settings['figure_show_ratings_global'], array(
								"inline" => TRUE, 
								"options" => array($locale['disable'], $locale['enable'])
							));
					
				// ['figure_344'] = "Social Sharing:";	--> figure_show_social_sharing_global
							// ALS CHECKBOX
								//echo form_checkbox('figure_show_social_sharing_global', $locale['figure_344'], $fil_settings['figure_show_social_sharing_global']);	
							// ALS DROPDOWN
							echo form_select("figure_show_social_sharing_global", $locale['figure_344'], $fil_settings['figure_show_social_sharing_global'], array(
								"inline" => TRUE, 
								"options" => array($locale['disable'], $locale['enable'])
							));								

	echo "</div>\n";
	echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>\n";

		/*---------------------------------------------------------------------------------------
		| 	´									| 	----------------------------------------	|
		| 										| 	|figure_show_affiliates_complete_global|	|
		| 										|	----------------------------------------	|
		| 										|												|
		| 										|	figure_show_affiliates_other_global			|
		| 										|	figure_show_amazon_global					|	
		| 										|	figure_show_ebay_global						|
		| 										|												|
		---------------------------------------------------------------------------------------*/
													
openside("");

	echo "<div class='alert alert-danger' role='alert'>";
				// $locale['figure_374'] = "Affiliate Complete Global"; --> figure_show_affiliates_complete_global
					// ALS CHECKBOX
						//echo form_checkbox("figure_show_affiliates_complete_global", $locale['figure_374'], $data['figure_show_affiliates_complete_global'], ["reverse_label" => true]);
						//echo form_checkbox("figure_show_affiliates_complete_global", $locale['figure_374'], $fil_settings['figure_show_affiliates_complete_global']);
					// ALS DROPDOWN 	
						
						echo form_select("figure_show_affiliates_complete_global", $locale['figure_374'], $fil_settings['figure_show_affiliates_complete_global'], array(
								"inline" => TRUE, 
								"options" => array($locale['disable'], $locale['enable'])
						));	
	echo "</div>\n";

	echo "<div class='alert alert-info' role='alert'>";	
				// ['figure_368'] = "Affiliate Other"; --> figure_show_affiliates_other_global
						// ALS CHECKBOX
							//echo form_checkbox('figure_show_affiliates_other_global', $locale['figure_368'], $fil_settings['figure_show_affiliates_other_global']);				
						// ALS DROPDOWN
							echo form_select("figure_show_affiliates_other_global", $locale['figure_368'], $fil_settings['figure_show_affiliates_other_global'], array(
								"inline" => TRUE, 
								"options" => array($locale['disable'], $locale['enable'])
							));
				
				// ['figure_369'] = "Affiliate Amazon"; --> figure_show_amazon_global
						// ALS CHECKBOX
							//echo form_checkbox('figure_show_amazon_global', $locale['figure_369'], $fil_settings['figure_show_amazon_global']);				
							// ALS DROPDOWN
							echo form_select("figure_show_amazon_global", $locale['figure_369'], $fil_settings['figure_show_amazon_global'], array(
									"inline" => TRUE, 
									"options" => array($locale['disable'], $locale['enable'])
								));
																
				// ['figure_370'] = " Affiliate Ebay"; --> figure_show_ebay_global
						// ALS CHECKBOX
							//echo form_checkbox('figure_show_ebay_global', $locale['figure_370'], $fil_settings['figure_show_ebay_global']);				
						// ALS DROPDOWN
							echo form_select("figure_show_ebay_global", $locale['figure_370'], $fil_settings['figure_show_ebay_global'], array(
								"inline" => TRUE, 
								"options" => array($locale['disable'], $locale['enable'])
							));	
							
	echo "</div>\n";
	
	closeside();							
										
	echo "</div>\n";	
	
	closeside();

	echo "</div>\n";	

// #######################################################################################################

// Display Figure Image Settings START
echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";	
	
	openside("<div class='text-bold text-uppercase'>".$locale['figure_377']."</div>");
	
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
/*echo "
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
</div>";*/

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
	
	
	echo "</div>\n";	

// Display Figure Image Settings END
	closeside();
// #######################################################################################################


// #######################################################################################################
// Display Category Image Settings START
echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
	
	openside("<div class='text-bold text-uppercase'>".$locale['figure_378']."</div>");
	
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
echo "</div>\n";	

// Display Category Image Settings END
	closeside();
// #######################################################################################################


// #######################################################################################################
// Display Manufacturer Image Settings START
echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";	
	
	openside("<div class='text-bold text-uppercase'>".$locale['figure_379']."</div>");

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

echo "</div>\n";	

// Display Manufacturer Image Settings END
	closeside();
// #######################################################################################################


// Display Notification Settings START ###################################################################
echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
	
	openside("<div class='text-bold text-uppercase'>".$locale['figure_380']."</div>");
	
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

echo form_button("savesettings", $locale['figure_345'], $locale['figure_345'], ["class" => "btn-success center-block"]);

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
