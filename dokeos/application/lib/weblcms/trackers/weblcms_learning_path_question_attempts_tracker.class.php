<?php
require_once Path :: get_tracking_path() . 'lib/main_tracker.class.php';

class WeblcmsLearningPathQuestionAttemptsTracker extends MainTracker
{	
	const PROPERTY_LPI_ATTEMPT_ID = 'lpi_attempt_id';
	const PROPERTY_QUESTION_CID = 'question_cid';
	const PROPERTY_ANSWER = 'answer';
	const PROPERTY_FEEDBACK = 'feedback';
	const PROPERTY_SCORE = 'score';
	
	/**
	 * Constructor sets the default values
	 */
    function WeblcmsLearningPathQuestionAttemptsTracker() 
    {
    	parent :: MainTracker('weblcms_lp_question_attempts');
    }
    
    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
    	$lpi_attempt_id = $parameters['lpi_attempt_id'];
    	$question_id = $parameters['question_cid'];
    	$answer = $parameters['answer'];
    	$feedback = $parameters['feedback'];
    	$score = $parameters['score'];
    	
    	$this->set_lpi_attempt_id($lpi_attempt_id);
    	$this->set_question_id($question_id);
    	$this->set_answer($answer);
    	$this->set_feedback($feedback);
    	$this->set_score($score);
    
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
    	return array_merge(parent :: get_default_property_names(), array(self :: PROPERTY_LPI_ATTEMPT_ID, self :: PROPERTY_QUESTION_CID,
    		self :: PROPERTY_ANSWER, self :: PROPERTY_FEEDBACK, self :: PROPERTY_SCORE));
    }

    function get_lpi_attempt_id()
    {
    	return $this->get_property(self :: PROPERTY_LPI_ATTEMPT_ID);
    }
 
    function set_lpi_attempt_id($lpi_attempt_id)
    {
    	$this->set_property(self :: PROPERTY_LPI_ATTEMPT_ID, $lpi_attempt_id);
    }
    
	function get_question_cid()
    {
    	return $this->get_property(self :: PROPERTY_QUESTION_CID);
    }
 
    function set_question_cid($question_cid)
    {
    	$this->set_property(self :: PROPERTY_QUESTION_CID, $question_cid);
    }
    
    function get_answer()
    {
    	return $this->get_property(self :: PROPERTY_ANSWER);
    }
 
    function set_answer($answer)
    {
    	$this->set_property(self :: PROPERTY_ANSWER, $answer);
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