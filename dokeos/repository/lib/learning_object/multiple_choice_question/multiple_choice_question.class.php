<?php
require_once dirname(__FILE__).'/../../learningobject.class.php';
require_once dirname(__FILE__).'/multiple_choice_question_option.class.php';
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
class MultipleChoiceQuestion extends LearningObject
{
	const PROPERTY_OPTIONS = 'options';
	public function add_option($option)
	{
		$options = $this->get_options();
		$options[] = $options;
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
}
?>