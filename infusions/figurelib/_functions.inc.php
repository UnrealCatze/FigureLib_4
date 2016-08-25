<?php

// Get Settings
$figurelibSettings = get_settings("figurelib");

// Stylesheets
add_to_head("
<!-- Figure Database -->
<style type='text/css'>
.figurelib-inforow-height {
	line-height: ".$figurelibSettings['figure_thumb2_h']."px;
}

.figurelib-inforow-height img {
	max-height: ".$figurelibSettings['figure_thumb2_h']."px !important;
}
</style>");
	
// Hande MyCollection Actions
if (iMEMBER) {
	
	// Add a Figure to Collection
	if (
		isset($_POST['add_figure']) && isNum($_POST['add_figure']) && 
		dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_id='".$_POST['add_figure']."' AND figure_freigabe='1'") &&
		!dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$_POST['add_figure']."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'")
	) {
		dbquery("
			INSERT INTO ".DB_FIGURE_USERFIGURES."
				(figure_userfigures_figure_id, figure_userfigures_user_id, figure_userfigures_language)
			VALUES
				('".$_POST['add_figure']."', '".$userdata['user_id']."', '".LANGUAGE."')
		");
		addNotice("success", "Figure is successfully added to your Collection.");
	
	// Delete a Figure from Collection
	} elseif (
		isset($_POST['remove_figure']) && isNum($_POST['remove_figure']) && 
		dbcount("(figure_id)", DB_FIGURE_ITEMS, "figure_id='".$_POST['remove_figure']."' AND figure_freigabe='1'") &&
		dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$_POST['remove_figure']."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'")
	) {
		dbquery("
			DELETE FROM ".DB_FIGURE_USERFIGURES."
			WHERE figure_userfigures_figure_id='".$_POST['remove_figure']."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'
		");
		addNotice("danger", "Figure is successfully removed from your Collection.");
	}
}

// Display my Collection Form
if (!@function_exists("figures_displayMyCollectionForm")) {
	function figures_displayMyCollectionForm($figureID, $formAction = FUSION_REQUEST, $formDesign = "mini") {
		global $locale, $userdata;

		// Standard Variables
		$return = "";
		// LANGUAGE
			if (file_exists(INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php")) {
				include INFUSIONS."figurelib/locale/".LOCALESET."locale_figurelib.php";
			} else {
				include INFUSIONS."figurelib/locale/English/locale_figurelib.php";
			}
		
		// Display Mini Form for Members
		if (iMEMBER && $formDesign == "mini") {
			$return .= openform("collectionform", "post", $formAction);
			if (dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$figureID."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'")) {
				$return .= form_button("remove_figure", "", $figureID, ["class" => "btn-xs btn-success", "alt" => $locale['userfigure_002'], "icon" => "fa fa-fw fa-check-square-o"]);
			} else {
				$return .= form_button("add_figure", "", $figureID, ["class" => "btn-xs btn-default", "alt" => $locale['userfigure_001'], "icon" => "fa fa-fw fa-plus-square-o"]);
			}
			$return .= closeform();
			
		// Display Big Form for Members
		} elseif (iMEMBER && $formDesign == "big") {
			$return .= openform("collectionform", "post", $formAction, ["class" => "text-center"]);
			if (dbcount("(figure_userfigures_id)", DB_FIGURE_USERFIGURES, "figure_userfigures_figure_id='".$figureID."' AND figure_userfigures_user_id='".$userdata['user_id']."' AND figure_userfigures_language='".LANGUAGE."'")) {
				$return .= form_button("remove_figure", $locale['userfigure_002'], $figureID, ["class" => "btn btn-sm btn-danger", "alt" => $locale['userfigure_002'], "icon" => ""]);
			} else {
				$return .= form_button("add_figure", $locale['userfigure_001'], $figureID, ["class" => "btn btn-sm btn-success", "alt" => $locale['userfigure_001'], "icon" => ""]);
			}
			$return .= "&nbsp;<a href='".INFUSIONS."figurelib/mycollection.php' class='btn btn-sm btn-primary' title='".$locale['userfigure_006']."'>".$locale['userfigure_006']."</a>\n";
			$return .= closeform();
		}
		
		// Return Form
		return $return;
	}
}

// Delete a single Image from Figure
if (!@function_exists("figures_deleteImage")) {
	function figures_deleteImage($imageID) {
		
		// Handle delete
		if (dbcount("(figure_images_image_id)", DB_FIGURE_IMAGES, "figure_images_image_id='".$imageID."'")) {
			$data = dbarray(dbquery("SELECT figure_images_image, figure_images_thumb, figure_images_thumb2 FROM ".DB_FIGURE_IMAGES." WHERE figure_images_image_id='".$imageID."' LIMIT 0,1"));
			if ($data['figure_images_image'] && @file_exists(FIGURES.$data['figure_images_image'])) {
				@unlink(FIGURES.$data['figure_images_image']);
			}
			if ($data['figure_images_thumb'] && @file_exists(THUMBS_FIGURES.$data['figure_images_thumb'])) {
				@unlink(THUMBS_FIGURES.$data['figure_images_thumb']);
			}
			if ($data['figure_images_thumb2'] && @file_exists(THUMBS_FIGURES.$data['figure_images_thumb2'])) {
				@unlink(THUMBS_FIGURES.$data['figure_images_thumb2']);
			}
			dbquery("DELETE FROM ".DB_FIGURE_IMAGES." WHERE figure_images_image_id='".$imageID."'");
		}
	}
}

// Delete all Images
if (!@function_exists("figures_deleteImages")) {
	function figures_deleteImages($section, $itemID) {
		
		// Handle each Section
		switch ($section) {
			
			// Categories
			case "cats":
				if (dbcount("(figure_cat_id)", DB_FIGURE_CATS, "figure_cat_id='".$itemID."'")) {
					$data = dbarray(dbquery("SELECT figure_cat_image, figure_cat_thumb FROM ".DB_FIGURE_CATS." WHERE figure_cat_id='".$itemID."' LIMIT 0,1"));
					if ($data['figure_cat_image'] && @file_exists(CATEGORIES.$data['figure_cat_image'])) {
						@unlink(CATEGORIES.$data['figure_cat_image']);
					}
					if ($data['figure_cat_thumb'] && @file_exists(THUMBS_CATEGORIES.$data['figure_cat_thumb'])) {
						@unlink(THUMBS_CATEGORIES.$data['figure_cat_thumb']);
					}
				}
				break;
				
			// Manufacturers
			case "manufacturers":
				if (dbcount("(figure_manufacturer_id)", DB_FIGURE_MANUFACTURERS, "figure_manufacturer_id='".$itemID."'")) {
					$data = dbarray(dbquery("SELECT figure_manufacturer_image, figure_manufacturer_thumb FROM ".DB_FIGURE_MANUFACTURERS." WHERE figure_manufacturer_id='".$itemID."' LIMIT 0,1"));
					if ($data['figure_manufacturer_image'] && @file_exists(MANUFACTURERS.$data['figure_manufacturer_image'])) {
						@unlink(MANUFACTURERS.$data['figure_manufacturer_image']);
					}
					if ($data['figure_manufacturer_thumb'] && @file_exists(THUMBS_MANUFACTURERS.$data['figure_manufacturer_thumb'])) {
						@unlink(THUMBS_MANUFACTURERS.$data['figure_manufacturer_thumb']);
					}
				}
				break;
			
			// Figures
			case "figures":
				$result = dbquery("SELECT figure_images_image, figure_images_thumb, figure_images_thumb2 FROM ".DB_FIGURE_IMAGES." WHERE figure_images_figure_id='".$itemID."'");
				if (dbrows($result)) {
					while ($data = dbarray($result)) {
						if ($data['figure_images_image'] && @file_exists(FIGURES.$data['figure_images_image'])) {
							@unlink(FIGURES.$data['figure_images_image']);
						}
						if ($data['figure_images_thumb'] && @file_exists(THUMBS_FIGURES.$data['figure_images_thumb'])) {
							@unlink(THUMBS_FIGURES.$data['figure_images_thumb']);
						}
						if ($data['figure_images_thumb2'] && @file_exists(THUMBS_FIGURES.$data['figure_images_thumb2'])) {
							@unlink(THUMBS_FIGURES.$data['figure_images_thumb2']);
						}
					}
					dbquery("DELETE FROM ".DB_FIGURE_IMAGES." WHERE figure_images_figure_id='".$itemID."'");
				}
				break;
				
		}
	}	
}

// Get correct Path to a Image
if (!@function_exists("figures_getImagePath")) {
	function figures_getImagePath($section, $image, $itemID, $counter = 1) {
		
		// Standard Variables
		$return = ""; $imgPath = false;
		
		// Handle each Section
		switch ($section) {
			
			// Manufacturer Image
			case "manufacturers":

				// Get Informations from Database
				$data = dbarray(dbquery("SELECT figure_manufacturer_image, figure_manufacturer_thumb FROM ".DB_FIGURE_MANUFACTURERS." WHERE figure_manufacturer_id='".$itemID."'"));
				
				// Thumb 1
				if ($image == "thumb" && !$imgPath) {
					if ($data['figure_manufacturer_thumb'] && @file_exists(THUMBS_MANUFACTURERS.$data['figure_manufacturer_thumb'])) {
						$imgPath = THUMBS_MANUFACTURERS.$data['figure_manufacturer_thumb'];
					} else {
						$image = "image";
					}
				}
				
				// Image
				if ($image == "image" && !$imgPath) {
					if ($data['figure_manufacturer_image'] && @file_exists(MANUFACTURERS.$data['figure_manufacturer_image'])) {
						$imgPath = MANUFACTURERS.$data['figure_manufacturer_image'];
					}
				}
				
				// If there is no Image, return Default-Image
				if (!$imgPath) {
					$imgPath = false;
				}
				
				// Return Image
				return $imgPath;
				break;
				
			// Category Image
			case "cats":

				// Get Informations from Database
				$data = dbarray(dbquery("SELECT figure_cat_image, figure_cat_thumb FROM ".DB_FIGURE_CATS." WHERE figure_cat_id='".$itemID."'"));
				
				// Thumb 1
				if ($image == "thumb" && !$imgPath) {
					if ($data['figure_cat_thumb'] && @file_exists(THUMBS_CATEGORIES.$data['figure_cat_thumb'])) {
						$imgPath = THUMBS_CATEGORIES.$data['figure_cat_thumb'];
					} else {
						$image = "image";
					}
				}
				
				// Image
				if ($image == "image" && !$imgPath) {
					if ($data['figure_cat_image'] && @file_exists(CATEGORIES.$data['figure_cat_image'])) {
						$imgPath = CATEGORIES.$data['figure_cat_image'];
					}
				}
				
				// If there is no Image, return Default-Image
				if (!$imgPath) {
					$imgPath = false;
				}
				
				// Return Image
				return $imgPath;
				break;

			// All Figure Images
			case "figures":
				
				// Get all Images
				$result = dbquery("
					SELECT
						figure_images_image_id
					FROM ".DB_FIGURE_IMAGES." 
					WHERE figure_images_figure_id='".$itemID."'
					".($counter != "all" ? " LIMIT 0,".$counter."" : "")."
				");
				
				// If there are Images, handle it
				if (dbrows($result)) {
					$return = array();
					while ($data = dbarray($result)) {
						$return[] = figures_getImagePath("figure-array", "thumb", $data['figure_images_image_id'], 1);
					}
				}
				
				// Give Array with all Images back
				return $return;
				break;
				
			// Get a single Image for a Figure 
			case "figure":
				$result = dbquery("SELECT figure_images_image, figure_images_thumb, figure_images_thumb2 FROM ".DB_FIGURE_IMAGES." WHERE figure_images_figure_id='".$itemID."' LIMIT 0,1");
				if (dbrows($result)) {
					$data = dbarray($result); $imgPath = false;
		
					// Thumb 2
					if ($image == "thumb2" && !$imgPath) {
						if ($data['figure_images_thumb2'] && @file_exists(THUMBS_FIGURES.$data['figure_images_thumb2'])) {
							$imgPath = THUMBS_FIGURES.$data['figure_images_thumb2'];
						} else {
							$image = "thumb";
						}
					}
					
					// Thumb 1
					if ($image == "thumb" && !$imgPath) {
						if ($data['figure_images_thumb'] && @file_exists(THUMBS_FIGURES.$data['figure_images_thumb'])) {
							$imgPath = THUMBS_FIGURES.$data['figure_images_thumb'];
						} else {
							$image = "image";
						}
					}
					
					// Image
					if ($image == "image" && !$imgPath) {
						if ($data['figure_images_image'] && @file_exists(FIGURES.$data['figure_images_image'])) {
							$imgPath = FIGURES.$data['figure_images_image'];
						}
					}
					
					// If there is no Image, return Default-Image
					if (!$imgPath) {
						$imgPath = INFUSIONS."figurelib/images/default.png";
					}
					
					return $imgPath;
				}
				break;
				
				
			// Give a Image with ID as Array back
			case "figure-array":
				$result = dbquery("SELECT figure_images_image, figure_images_thumb, figure_images_thumb2 FROM ".DB_FIGURE_IMAGES." WHERE figure_images_image_id='".$itemID."' LIMIT 0,1");
				if (dbrows($result)) {
					$data = dbarray($result); $imgPath = false;
		
					// Thumb 2
					if ($image == "thumb2" && !$imgPath) {
						if ($data['figure_images_thumb2'] && @file_exists(THUMBS_FIGURES.$data['figure_images_thumb2'])) {
							$imgPath = THUMBS_FIGURES.$data['figure_images_thumb2'];
						} else {
							$image = "thumb";
						}
					}
					
					// Thumb 1
					if ($image == "thumb" && !$imgPath) {
						if ($data['figure_images_thumb'] && @file_exists(THUMBS_FIGURES.$data['figure_images_thumb'])) {
							$imgPath = THUMBS_FIGURES.$data['figure_images_thumb'];
						} else {
							$image = "image";
						}
					}
					
					// Image
					if ($image == "image" && !$imgPath) {
						if ($data['figure_images_image'] && @file_exists(FIGURES.$data['figure_images_image'])) {
							$imgPath = FIGURES.$data['figure_images_image'];
						}
					}
					
					// If there is no Image, return Default-Image
					if (!$imgPath) {
						$imgPath = INFUSIONS."figurelib/images/default.png";
					}
					
					return ["id" => $itemID, "image" => $imgPath];
				}
				break;
			
		}
	}
}

// Get Name of a Country
if (!@function_exists("figures_getCountryName")) {
	function figures_getCountryName($countryID) {
		
		// Get Array with all Countries
		$countries = array();
		require_once INCLUDES."geomap/geomap.inc.php";
	
		// Check if Country exists
		if (isset($countries[$countryID])) {
			return $countries[$countryID];
		} else {
			return "Sorry! We don't know this Country.";
		}
	}
}

?>
