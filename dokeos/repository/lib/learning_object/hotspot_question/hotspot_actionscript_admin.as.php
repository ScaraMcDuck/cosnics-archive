<?php
	include_once dirname(__FILE__).'/../../../../common/global.inc.php';
	$picturePath   = $_SESSION['full_path'];
	$hotspotImagePath = $_SESSION['hotspot_path'];
	$pictureParts = split('/', $picturePath);
	$pictureName   = $pictureParts[count($pictureParts) - 1];
	$pictureSize   = getimagesize($picturePath);
	$pictureWidth  = $pictureSize[0];
	$pictureHeight = $pictureSize[1];
	
	$courseLang = Translation :: get_language();
	$courseCode = 'false';
	$coursePath = 'false';
	$output = "hotspot_lang=".$courseLang."&hotspot_image=".$pictureName."&hotspot_user=".$pictureParts[count($pictureParts) - 2]."&hotspot_image_width=".$pictureWidth."&hotspot_image_height=".$pictureHeight."&courseCode=".$coursePath;
	$i = 0;
	$nmbrTries = 0;
	
	$answers = $_SESSION['answers'];
	$weights = $_SESSION['weights'];
	$types = $_SESSION['types'];
	$coordinates = $_SESSION['coordinates'];
	$nbrAnswers = $_SESSION['mc_num_options'];
	
	for($i = 0;$i < $nbrAnswers;$i++)
	{
	   	$output .= "&hotspot_".($i+1)."=true";
		$output .= "&hotspot_".($i+1)."_answer=".$answers[$i];
	
		if ($types[$i] != null)
			$output .= "&hotspot_".($i+1)."_type=".$types[$i];
		else
			$output .= "&hotspot_".($i+1)."_type=square";
		// This is a good answer, count + 1 for nmbr of clicks
		if ($weights[$i] > 0)
		{
			$nmbrTries++;
		}
		
		if ($coordinates[$i] != null)
			$output .= "&hotspot_".($i+1)."_coord=".$coordinates[$i];
		else
			$output .= "&hotspot_".($i+1)."_coord=0;0|0|0";
	}
	
	// Generate empty
	$i++;
	for ($i; $i <= 12; $i++)
	{
		$output .= "&hotspot_".$i."=false";
	}
	// Output
	echo $output."&nmbrTries=".$nmbrTries."&done=done";
?>