<?php
require_once dirname(__FILE__).'/../../learning_object.class.php';
require_once dirname(__FILE__).'/multiple_choice_question_option.class.php';
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
class MultipleChoiceQuestion extends LearningObject
{
	const PROPERTY_ANSWER_TYPE = 'answer_type';
	const PROPERTY_OPTIONS = 'options';
	
	public function add_option($option)
	{
		$options = $this->get_options();
		$options[] = $option;
		return $this->set_additional_property(self :: PROPERTY_OPTIONS, serialize($options));
	}
	public function set_options($options)
	{
		return $this->set_additional_property(self :: PROPERTY_OPTIONS, serialize($options));
	}
	public function get_options()
	{
		if($result = unserialize($this->get_additional_property(self :: PROPERTY_OPTIONS)))
		{
			return $result;
		}
		return array();
	}
	public function get_number_of_options()
	{
		return count($this->get_options());
	}
	public function set_answer_type($type)
	{
		return $this->set_additional_property(self :: PROPERTY_ANSWER_TYPE, $type);
	}
	public function get_answer_type($type)
	{
		return $this->get_additional_property(self :: PROPERTY_ANSWER_TYPE);
	}
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_ANSWER_TYPE, self :: PROPERTY_OPTIONS);
	}
}
?>