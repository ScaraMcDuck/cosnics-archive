<?php
include_once dirname(__FILE__).'/../../../../../../../common/global.inc.php';
require_once dirname(__FILE__).'/../../../../trackers/weblcms_assessment_attempts_tracker.class.php';

$tracker_id = Request :: get('tracker');
//$user_id = Request :: get('user_id');
$score = Request :: get('score');
$course = Request :: get('course');

//dump($_GET);

$condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ID, $tracker_id);
//dump($condition);
$track = new WeblcmsAssessmentAttemptsTracker();
//dump($track);
$trackers = $track->retrieve_tracker_items($condition);
//dump($trackers);

if ($trackers[0] != null)
{
	$tracker = $trackers[0];
	$tracker->set_total_score($score);
	$tracker->update();
	//echo 'tracker updated';
	//dump($tracker);
}
$path = Path :: get(WEB_PATH) . 'run.php?course=' . $course . '&go=courseviewer&application=weblcms&tool=assessment';

echo '<script language="JavaScript" type="text/javascript">'."window.open('$path', '_top', '')".'</script>';
?>