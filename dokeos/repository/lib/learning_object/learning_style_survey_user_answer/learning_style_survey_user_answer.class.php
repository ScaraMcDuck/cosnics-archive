<?php

require_once dirname(__FILE__) . '/../../learningobject.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyUserAnswer extends LearningObject
{
	// Can also be the ID of an answer!
	const PROPERTY_RESULT_ID = 'result_id';
	const PROPERTY_QUESTION_ID = 'question_id';
	const PROPERTY_ANSWER = 'answer';
	
	function get_result_id ()
	{
		return $this->get_additional_property(self :: PROPERTY_RESULT_ID);
	}

	function get_question_id ()
	{
		return $this->get_additional_property(self :: PROPERTY_QUESTION_ID);
	}
	
	function get_answer ()
	{
		return $this->get_additional_property(self :: PROPERTY_ANSWER);
	}
	
	function set_result_id ($pid)
	{
		return $this->set_additional_property(self :: PROPERTY_RESULT_ID, $pid);
	}
	
	function set_question_id ($qid)
	{
		return $this->set_additional_property(self :: PROPERTY_QUESTION_ID, $qid);
	}
	
	function set_answer ($answer)
	{
		return $this->set_additional_property(self :: PROPERTY_ANSWER, $answer);
	}
	
	function is_versionable()
	{
		return false;
	}
	
	function is_master_type()
	{
		return false;
	}
}

?>