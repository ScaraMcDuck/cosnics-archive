<?php
require_once Path :: get_tracking_path() . 'lib/main_tracker.class.php';

class WeblcmsLearningPathAssessmentAttemptsTracker extends MainTracker
{
	// Can be used for subscribsion of users / classes
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_LEARNING_PATH_ID = 'learning_path_id';
	const PROPERTY_COURSE_ID = 'course_id';
	const PROPERTY_ASSESSMENT_ID = 'assessment_id';
	const PROPERTY_DATE = 'date';
	const PROPERTY_TOTAL_SCORE = 'total_score';
	
	/**
	 * Constructor sets the default values
	 */
    function WeblcmsLearningPathAssessmentAttemptsTracker() 
    {
    	parent :: MainTracker('weblcms_lp_assessment_attempts');
    }
    
    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
    	$user = $parameters['user_id'];
    	$learning_path = $parameters['learning_path_id'];
    	$assessment = $parameters['assessment_id'];
    	$total_score = $parameters['total_score'];
    	$course_id = $parameters['course_id'];
    	
    	$this->set_user_id($user);
    	$this->set_course_id($course_id);
    	$this->set_learning_path_id($learning_path);
    	$this->set_assessment_id($assessment);
    	
    	$this->set_date(DatabaseRepositoryDataManager :: to_db_date(time()));
    	$this->set_total_score($total_score);
    	//dump($this);
    	
    	$this->create();
    	
    	return $this->get_id();
    }
    
    /**
     * Inherited
     * @see MainTracker :: is_summary_tracker
     */
    function is_summary_tracker()
    {
    	return false;
    }
    
    /**
     * Inherited
     */
    function get_default_property_names()
    {
    	return array_merge(parent :: get_default_property_names(), array(self :: PROPERTY_USER_ID, self :: PROPERTY_LEARNING_PATH_ID,
    		self :: PROPERTY_COURSE_ID, self :: PROPERTY_ASSESSMENT_ID, self :: PROPERTY_DATE, self :: PROPERTY_TOTAL_SCORE));
    }

    function get_user_id()
    {
    	return $this->get_property(self :: PROPERTY_USER_ID);
    }
 
    function set_user_id($user_id)
    {
    	$this->set_property(self :: PROPERTY_USER_ID, $user_id);
    }
    
	function get_learning_path_id()
    {
    	return $this->get_property(self :: PROPERTY_LEARNING_PATH_ID);
    }
 
    function set_learning_path_id($learning_path_id)
    {
    	$this->set_property(self :: PROPERTY_LEARNING_PATH_ID, $learning_path_id);
    }
    
    function get_assessment_id()
    {
    	return $this->get_property(self :: PROPERTY_ASSESSMENT_ID);
    }
 
    function set_assessment_id($assessment_id)
    {
    	$this->set_property(self :: PROPERTY_ASSESSMENT_ID, $assessment_id);
    }
    
    function get_date()
    {
    	return $this->get_property(self :: PROPERTY_DATE);
    }
 
    function set_date($date)
    {
    	$this->set_property(self :: PROPERTY_DATE, $date);
    }
    
    function get_total_score()
    {
    	return $this->get_property(self :: PROPERTY_TOTAL_SCORE);
    }
 
    function set_total_score($total_score)
    {
    	$this->set_property(self :: PROPERTY_TOTAL_SCORE, $total_score);
    }
    
    function get_course_id()
    {
    	return $this->get_property(self :: PROPERTY_COURSE_ID);
    }
 
    function set_course_id($course_id)
    {
    	$this->set_property(self :: PROPERTY_COURSE_ID, $course_id);
    }
    
    function empty_tracker($event)
    {
    	$this->remove();
    }
}
?>