<?php
include_once dirname(__FILE__).'/../../../../common/global.inc.php';

require_once Path :: get_application_path().'lib/weblcms/trackers/weblcms_question_attempts_tracker.class.php';

// set vars
$userId        = 0;//$_user['user_id'];
$clo_questionId    = $_GET['modifyAnswers'];
$exe_id    = $_GET['exe_id'];

$clo_question = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_item($clo_questionId);
$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());//Question :: read($questionId);
$documentPath  = //api_get_path(SYS_COURSE_PATH).$_course['path'].'/document';

$picture = $question->get_image();
$parts = split('/', $picture);
$pictureName = $parts[1];
$picturePath = $picture;
$fullPath = Path :: get(SYS_REPO_PATH).$picture;
$pictureSize   = getimagesize($fullPath);
$pictureWidth  = $pictureSize[0];
$pictureHeight = $pictureSize[1];

$courseLang = Translation :: get_language();//$_course['language'];
$courseCode = '';//$_course['sysCode'];
$coursePath = ''; //$_course['path'];

// Init
$output = "hotspot_lang=$courseLang&hotspot_image=$picture&hotspot_image_width=$pictureWidth&hotspot_image_height=$pictureHeight&courseCode=$coursePath";

$answers = $question->get_answers();

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
	$output .= "&hotspot_".$i."=false";
}

// set vars
$questionId    = $_GET['modifyAnswers'];
$course_code = ''; //$_course['id'];

$track = new WeblcmsQuestionAttemptsTracker();
$conditiona = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $exe_id);
$conditionq = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_QUESTION_ID, $clo_question->get_id());
$condition = new AndCondition(array($conditionq, $conditiona));
$items = $track->retrieve_tracker_items($condition);

foreach ($items as $item)
{
	$answer = $item->get_answer();
	$parts = split('-', $answer);
	$output2 .= $parts[0]."|";
}

$output .= "&p_hotspot_answers=".substr($output2,0,-1)."&done=done";

$explode = explode('&', $output);

echo $output;

?>