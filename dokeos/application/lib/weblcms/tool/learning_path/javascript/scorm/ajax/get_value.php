<?php
require_once dirname(__FILE__) . '/../../../../../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_lpi_attempt_tracker.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

$tracker_id = Request :: post('tracker_id');
$variable = Request :: post('variable');

$condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_ID, $tracker_id);		
$dummy = new WeblcmsLpiAttemptTracker();
$trackers = $dummy->retrieve_tracker_items($condition);
$tracker = $trackers[0];

$rdm = RepositoryDataManager :: get_instance();
$item = $rdm->retrieve_complex_learning_object_item($tracker->get_lp_item_id());

$learning_path_item = $rdm->retrieve_learning_object($item->get_ref());
$scorm_item = $rdm->retrieve_learning_object($learning_path_item->get_reference());

if(substr($variable, 0, 15) == 'cmi.objectives.')
{
	$left = substr($variable, 15, strlen($variable) - 15);
	$objectives = $scorm_item->get_objectives();
	
	$first_char = substr($left, 0, 1);
	if(is_numeric($first_char))
	{
		$objective = $objectives->get_objective(intval($first_char));
		$left = substr($left, 2, strlen($left) - 2);
	}
	
	switch($left)
	{
		case '_count':
			$value = $objectives ? $objectives->count_objectives() : 0;
			break;
		case 'id':
			$value = $objective->get_id();
	}
}
else 
{
	switch($variable)
	{
		case 'cmi.max_time_allowed' : 
			$value = $scorm_item->get_time_limit(); 
			break;
		case 'cmi.scaled_passing_score' : 
			$objectives = $scorm_item->get_objectives();
			if($objectives)
			{
				$primary = $objectives->get_primary_objective();
				if($primary->get_satisfied_by_measure())
					$value = $primary->get_minimum_satisfied_measure();
			}
			break;
	}
}

echo $value;
?>