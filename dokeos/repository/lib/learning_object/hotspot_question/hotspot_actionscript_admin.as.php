<?php
	$picturePath   = $_SESSION['hotspot_path'];
	$pictureParts = split('/', $picturePath);
	$pictureName   = $pictureParts[count($pictureParts) - 1];
	$pictureSize   = getimagesize($picturePath);
	$pictureWidth  = $pictureSize[0];
	$pictureHeight = $pictureSize[1];
	
	//$courseLang = Translation :: get_language();
	//$courseCode = $_course['sysCode'];
	//$coursePath = $_course['path'];
	$courseLang = 'false';
	$courseCode = 'false';
	$coursePath = 'false';
	$output = "hotspot_lang=".$courseLang."&hotspot_image=".$picturePath."&hotspot_image_width=".$pictureWidth."&hotspot_image_height=".$pictureHeight."&courseCode=".$coursePath;
	$i = 0;
	$nmbrTries = 0;
	
	$answers = $_POST['answer'];
	//dump($answers);
	$weights = $_POST['weight'];
	$types = $_POST['type'];
	$coordinates = $_POST['coordinates'];
	$nbrAnswers = count($answers);
	
	for($i = 0;$i < $nbrAnswers;$i++)
	{
	   	$output .= "&hotspot_".$i."=true";
		$output .= "&hotspot_".$i."_answer=".$answers[$i];
	
		$output .= "&hotspot_".$i."_type=".$types[$i];
		// This is a good answer, count + 1 for nmbr of clicks
		if ($weights[$i] > 0)
		{
			$nmbrTries++;
		}
	
		$output .= "&hotspot_".$i."_coord=".$coordinates[$i]."";
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