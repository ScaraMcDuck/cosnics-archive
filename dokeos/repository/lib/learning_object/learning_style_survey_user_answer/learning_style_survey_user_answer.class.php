<?php

require_once dirname(__FILE__) . '/../../learning_object.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyUserAnswer extends LearningObject
{
	const PROPERTY_QUESTION_ID = 'question_id';
	const PROPERTY_ANSWER = 'answer';
	
	private $question;
	
	function get_question ()
	{
		if (!$this->question)
		{
			$dm = RepositoryDataManager :: get_instance();
			$this->question = $dm->retrieve_learning_object($this->get_question_id());
		}
		return $this->question;
	}
	
	function get_question_id ()
	{
		return $this->get_additional_property(self :: PROPERTY_QUESTION_ID);
	}
	
	function get_answer ()
	{
		return $this->get_additional_property(self :: PROPERTY_ANSWER);
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
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_ANSWER, self :: PROPERTY_QUESTION_ID);
	}
}

?>