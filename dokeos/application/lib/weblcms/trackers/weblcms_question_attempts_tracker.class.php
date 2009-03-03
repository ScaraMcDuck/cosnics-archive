<?php
 
require_once Path :: get_tracking_path() . 'lib/main_tracker.class.php';

class WeblcmsQuestionAttemptsTracker extends MainTracker
{	
	const PROPERTY_ASSESSMENT_ATTEMPT_ID = 'assessment_attempt_id';
	const PROPERTY_QUESTION_ID = 'question_id';
	const PROPERTY_ANSWER_INDEX = 'answer_idx';
	const PROPERTY_ANSWER = 'answer';
	const PROPERTY_FEEDBACK = 'feedback';
	const PROPERTY_SCORE = 'score';
	const PROPERTY_DATE = 'date';
	
	/**
	 * Constructor sets the default values
	 */
    function WeblcmsQuestionAttemptsTracker() 
    {
    	parent :: MainTracker('weblcms_question_attempts');
    }
    
    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
    	$assessment_attempt_id = $parameters['assessment_attempt_id'];
    	$question_id = $parameters['question_id'];
    	$answer = $parameters['answer'];
    	$answer_idx = $parameters['answer_idx'];
    	$feedback = $parameters['feedback'];
    	$score = $parameters['score'];
    	
    	$this->set_assessment_attempt_id($assessment_attempt_id);
    	$this->set_question_id($question_id);
    	$this->set_answer($answer);
    	$this->set_answer_index($answer_idx);
    	$this->set_feedback($feedback);
    	$this->set_score($score);
    	$this->set_date(DatabaseRepositoryDataManager :: to_db_date(time()));
    	
    	$this->create();
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
    	return array_merge(parent :: get_default_property_names(), array(self :: PROPERTY_ASSESSMENT_ATTEMPT_ID, self :: PROPERTY_QUESTION_ID,
    		self :: PROPERTY_ANSWER, self :: PROPERTY_ANSWER_INDEX, self :: PROPERTY_FEEDBACK, self :: PROPERTY_SCORE, self :: PROPERTY_DATE));
    }

    function get_assessment_attempt_id()
    {
    	return $this->get_property(self :: PROPERTY_ASSESSMENT_ATTEMPT_ID);
    }
 
    function set_assessment_attempt_id($assessment_attempt_id)
    {
    	$this->set_property(self :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $assessment_attempt_id);
    }
    
	function get_question_id()
    {
    	return $this->get_property(self :: PROPERTY_QUESTION_ID);
    }
 
    function set_question_id($question_id)
    {
    	$this->set_property(self :: PROPERTY_QUESTION_ID, $question_id);
    }
    
    function get_answer()
    {
    	return $this->get_property(self :: PROPERTY_ANSWER);
    }
 
    function set_answer($answer)
    {
    	$this->set_property(self :: PROPERTY_ANSWER, $answer);
    }
    
    function get_answer_index()
    {
    	return $this->get_property(self :: PROPERTY_ANSWER_INDEX);
    }
    
    function set_answer_index($answer_index)
    {
    	$this->set_property(self :: PROPERTY_ANSWER_INDEX, $answer_index);
    }
    
    function get_date()
    {
    	return $this->get_property(self :: PROPERTY_DATE);
    }
 
    function set_date($date)
    {
    	$this->set_property(self :: PROPERTY_DATE, $date);
    }
    
    function get_score()
    {
    	return $this->get_property(self :: PROPERTY_SCORE);
    }
 
    function set_score($score)
    {
    	$this->set_property(self :: PROPERTY_SCORE, $score);
    }
    
    function get_feedback()
    {
    	return $this->get_property(self :: PROPERTY_FEEDBACK);
    }
 
    function set_feedback($feedback)
    {
    	$this->set_property(self :: PROPERTY_FEEDBACK, $feedback);
    }
    
    function empty_tracker($event)
    {
    	$this->remove();
    }
}
?>