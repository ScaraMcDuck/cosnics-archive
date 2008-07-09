<?php
require_once dirname(__FILE__).'/../../learning_object.class.php';
require_once dirname(__FILE__).'/matching_question_option.class.php';
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
class MatchingQuestion extends LearningObject
{
	const PROPERTY_OPTIONS = 'options';
	const PROPERTY_MATCHES = 'matches';
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
	public function get_number_of_options()
	{
		return count($this->get_options());
	}
	public function add_match($match)
	{
		$matches = $this->get_matches();
		$matches[] = $matches;
		return $this->set_additional_property(self :: PROPERTY_MATCHES, serialize($matches));
	}
	public function set_matches($matches)
	{
		return $this->set_additional_property(self :: PROPERTY_MATCHES, serialize($matches));
	}
	public function get_matches()
	{
		if($result = unserialize($this->get_additional_property(self :: PROPERTY_MATCHES)))
		{
			return $result;
		}
		return array();
	}
	public function get_number_of_matches()
	{
		return count($this->get_matches());
	}
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_MATCHES, self :: PROPERTY_OPTIONS);
	}
}
?>