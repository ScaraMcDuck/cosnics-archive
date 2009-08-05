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
    	$dummy = new AssessmentAssessmentAttemptsTracker();
    	$condition = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $aid);
    	$trackers = $dummy->retrieve_tracker_items($condition);
    	
    	foreach($trackers as $tracker)
    	{
    		$user = UserDataManager :: get_instance()->retrieve_user($tracker->get_user_id());
    		$data[Translation :: get('User')][] = $user->get_fullname();
	    	$data[Translation :: get('Date')][] = $tracker->get_date();
	    	$data[Translation :: get('TotalScore')][] = $tracker->get_total_score();
	    	$data[Translation :: get('Action')][] = '';
    	}
    	
    	$description[Reporting::PARAM_ORIENTATION] = Reporting::ORIENTATION_HORIZONTAL;
        return Reporting :: getSerieArray($data, $description);
    }
    
    public static function getSummaryAssessmentAttempts($params)
    {
    	$data = array();
		$category = $params['category'];
    	
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
	    	$data[Translation :: get('Action')][] = '';
    	}
    	
    	$description[Reporting::PARAM_ORIENTATION] = Reporting::ORIENTATION_HORIZONTAL;
        return Reporting :: getSerieArray($data, $description);
    }

}
?>
