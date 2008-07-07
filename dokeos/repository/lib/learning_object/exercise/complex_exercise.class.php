<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';
/**
 * This class represents a complex exercise (used to create complex learning objects)
 */
class ComplexExercise extends ComplexLearningObjectItem
{

	const PROPERTY_TEST = 'test';

	function get_test()
	{
		return $this->get_additional_property(self :: PROPERTY_TEST);
	}

	function set_test($test)
	{
		return $this->set_additional_property(self :: PROPERTY_TEST, $test);
	}
	
	static function get_additional_property_names()
	{
		return array(self :: PROPERTY_TEST);
	}
	
	function get_allowed_types()
	{
		return array('fill_in_blanks_question', 'matching_question', 'multiple_choice_question',
					 'open_question');
	}
}
?>