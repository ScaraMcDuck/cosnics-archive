<?php
	include_once dirname(__FILE__).'/../../../../common/global.inc.php';
	
	// set vars
	$clo_questionId = Request :: get('modifyAnswers');
	$clo_question = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_item($clo_questionId);
	//$objQuestion = Question::read($questionId);
	$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
	//$TBL_ANSWERS   = Database::get_course_table(TABLE_QUIZ_ANSWER);
	//$documentPath  = api_get_path(SYS_COURSE_PATH).$_course['path'].'/document';
	//$picturePath   = $documentPath.'/images';
	//$pictureName   = $objQuestion->selectPicture();
	$picture = $question->get_image();
	$parts = split('/', $picture);
	$pictureName = $parts[1];
	$picturePath = $picture;
	$fullPath = Path :: get(SYS_REPO_PATH).$picture;
	$pictureSize   = getimagesize($fullPath);
	$pictureWidth  = $pictureSize[0];
	$pictureHeight = $pictureSize[1];
	
	$courseLang = Translation :: get_language();
	$courseCode = ''; //$_course['sysCode'];
	$coursePath = ''; //$_course['path'];
	
	// Query db for answers
	//$sql = "SELECT id, answer, hotspot_coordinates, hotspot_type, ponderation FROM $TBL_ANSWERS WHERE question_id = '$questionId' ORDER BY id";
	//$result = api_sql_query($sql,__FILE__,__LINE__);
	
	// Init
	$output = "hotspot_lang=$courseLang&hotspot_image=$picture&hotspot_image_width=$pictureWidth&hotspot_image_height=$pictureHeight&courseCode=$coursePath";
	$i = 0;
	
	$answers = $question->get_answers();
	$nmbrTries = count($answers);
	for ($i = 0; $i < count($answers); $i++)
	{
	   	$output .= "&hotspot_".($i+1)."=true";
		$output .= "&hotspot_".($i+1)."_answer=".str_replace('&','{amp}',$answers[$i]->get_answer());	
		$output .= '&hotspot_'.($i+1).'_type='.$answers[$i]->get_hotspot_type();
		$output .= "&hotspot_".($i+1)."_coord=".$answers[$i]->get_hotspot_coordinates();
	}
	
	// Generate empty
	$i++;
	for ($i; $i <= 12; $i++)
	{
		$output .= "&hotspot_".($i+1)."=false";
	}
	
	// Output
	echo $output."&nmbrTries=".$nmbrTries."&done=done";

?>