<?php
require_once dirname(__FILE__) . '/../../learning_object.class.php';
require_once dirname(__FILE__) .'/fill_in_blanks_answer.class.php';
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
class FillInBlanksQuestion extends LearningObject
{
	const PROPERTY_ANSWERS = 'answers';
	
	public function add_answer($answer)
	{
		$answers = $this->get_answers();
		$answers[] = $answer;
		return $this->set_additional_property(self :: PROPERTY_ANSWERS, serialize($answers));
	}
	
	public function set_answers($answers)
	{
		return $this->set_additional_property(self :: PROPERTY_ANSWERS, serialize($answers));
	}
	
	public function get_answers()
	{
		if($result = unserialize($this->get_additional_property(self :: PROPERTY_ANSWERS)))
		{
			return $result;
		}
		return array();
	}
	
	public function get_number_of_answers()
	{
		return count($this->get_answers());
	}
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_ANSWERS);
	}
}
?>