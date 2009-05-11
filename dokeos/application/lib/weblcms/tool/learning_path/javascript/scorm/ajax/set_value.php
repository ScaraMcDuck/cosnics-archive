<?php
require_once dirname(__FILE__) . '/../../../../../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_lpi_attempt_tracker.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

$tracker_id = Request :: post('tracker_id');
$variable = Request :: post('variable');
$value = Request :: post('value');

$condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_ID, $tracker_id);		
$dummy = new WeblcmsLpiAttemptTracker();
$trackers = $dummy->retrieve_tracker_items($condition);
$tracker = $trackers[0];

$rdm = RepositoryDataManager :: get_instance();
$item = $rdm->retrieve_complex_learning_object_item($tracker->get_lp_item_id());

$learning_path_item = $rdm->retrieve_learning_object($item->get_ref());
$scorm_item = $rdm->retrieve_learning_object($learning_path_item->get_reference());

switch($variable)
{
	case 'cmi.success_status':
		$tracker->set_status('completed');
		$tracker->set_total_time($tracker->get_total_time() + (time() - $tracker->get_start_time()));
		$tracker->update();
		
		$objs = $scorm_item->get_objectives();
		if($objs)
		{
			$objectives = $objs->get_objectives();

			foreach($objectives as $index => $objective)
			{
				if($objective && $objective->get_contributes_to_rollup())
				{
					$parameters = array('lpi_view_id' => $tracker->get_id(), 'objective_id' => $objective->get_id(), 'status' => 'completed', 'display_order' => $index);
					Events :: trigger_event('attempt_learning_path_item_objective', 'weblcms', $parameters);
				}
			}
		}

		break;
}

?>