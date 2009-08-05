<?php
/**
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__) . '/../assessment_data_manager.class.php';
require_once dirname(__FILE__) . '/../assessment_manager/assessment_manager.class.php';
require_once dirname(__FILE__) . '/../trackers/assessment_assessment_attempts_tracker.class.php';

class ReportingAssessment
{

    function ReportingAssessment() 
    {
    	
    }
    
    public static function getAssessmentAttempts($params)
    {
    	$aid = $params[AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION];
    	$url = $params['url'];
    	
    	$dummy = new AssessmentAssessmentAttemptsTracker();
    	$condition = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $aid);
    	$trackers = $dummy->retrieve_tracker_items($condition);
    	
    	foreach($trackers as $tracker)
    	{
    		$user = UserDataManager :: get_instance()->retrieve_user($tracker->get_user_id());
    		$data[Translation :: get('User')][] = $user->get_fullname();
	    	$data[Translation :: get('Date')][] = $tracker->get_date();
	    	$data[Translation :: get('TotalScore')][] = $tracker->get_total_score() . '%';
	    	
	    	$actions = array();
	    	
	    	$actions[] = array(
				'href' => $url . '&details=' . $tracker->get_id(),
				'label' => Translation :: get('ViewResults'),
				'img' => Theme :: get_common_image_path().'action_view_results.png'
			);
			
			$actions[] = array(
				'href' =>  $url . '&delete=tid_' . $tracker->get_id(),
				'label' => Translation :: get('DeleteResults'),
				'img' => Theme :: get_common_image_path().'action_delete.png'
			);
			
			$actions[] = array(
				'href' => '',
				'label' => Translation :: get('ExportResults'),
				'img' => Theme :: get_common_image_path().'action_export.png'
			);
	    	
	    	$data[Translation :: get('Action')][] = DokeosUtilities :: build_toolbar($actions);
    	}
    	
    	$description[Reporting::PARAM_ORIENTATION] = Reporting::ORIENTATION_HORIZONTAL;
        return Reporting :: getSerieArray($data, $description);
    }
    
    public static function getSummaryAssessmentAttempts($params)
    {
    	$data = array();
		$category = $params['category'];
    	$url = $params['url'];
		
    	$adm = AssessmentDataManager :: get_instance();
    	$condition = new EqualityCondition(AssessmentPublication :: PROPERTY_CATEGORY, $category);
    	$publications = $adm->retrieve_assessment_publications($condition);
    	$dummy = new AssessmentAssessmentAttemptsTracker();
    	
    	while($publication = $publications->next_result())
    	{
	    	$lo = $publication->get_publication_object();
	    	$type = $lo->get_type();
	    	if($type == 'assessment')
	    	{
	    		$type = $lo->get_assessment_type();
	    	}
	    	
    		$data[Translation :: get('Type')][] = Translation :: get($type);
	    	$data[Translation :: get('Title')][] = $lo->get_title();
	    	$data[Translation :: get('TimesTaken')][] = $dummy->get_times_taken($publication);
	    	$data[Translation :: get('AverageScore')][] = $dummy->get_average_score($publication) . '%';
	    	
	    	$actions = array();
	    	
	    	$actions[] = array(
				'href' => $url . '&' . AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION . '=' . $publication->get_id(),
				'label' => Translation :: get('ViewResults'),
				'img' => Theme :: get_common_image_path().'action_view_results.png'
			);
			
			$actions[] = array(
				'href' => $url . '&delete=aid_' . $publication->get_id(),
				'label' => Translation :: get('DeleteResults'),
				'img' => Theme :: get_common_image_path().'action_delete.png'
			);
	    	
	    	$data[Translation :: get('Action')][] = DokeosUtilities :: build_toolbar($actions);
    	}
    	
    	$description[Reporting::PARAM_ORIENTATION] = Reporting::ORIENTATION_HORIZONTAL;
        return Reporting :: getSerieArray($data, $description);
    }

}
?>
