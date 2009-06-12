<?php
/**
 * @package repository.learningobject
 * @subpackage assessment
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';
/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexAssessment extends ComplexLearningObjectItem
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
		return array('open_question', 'hotspot_question', 'fill_in_blanks_question', 'multiple_choice_question', 
					 'matching_question', 'select_question', 'matrix_question');
	}
}
?>