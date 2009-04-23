<?php
require_once dirname(__FILE__) . '/../../../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_lpi_attempt_tracker.class.php';

$tracker_id = $_POST['tracker_id'];

$condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_ID, $tracker_id);
		
$dummy = new WeblcmsLpiAttemptTracker();
$trackers = $dummy->retrieve_tracker_items($condition);
$tracker = $trackers[0];
$tracker->set_status('completed');
$tracker->set_total_time($tracker->get_total_time() + (time() - $tracker->get_start_time()));
$tracker->update();

?>