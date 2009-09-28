<?php
/**
 * @package repository.learningobject
 * @subpackage assessment
 */
require_once dirname(__FILE__) . '/../../complex_content_object_item.class.php';
/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexSurvey extends ComplexContentObjectItem
{

	/*const PROPERTY_ANONYMOUS = 'anonymous';

	function get_anonymous()
	{
		return $this->get_additional_property(self :: PROPERTY_ANONYMOUS);
	}

	function set_anonymous($value)
	{
		return $this->set_additional_property(self :: PROPERTY_ANONYMOUS, $value);
	}*/
	
	static function get_additional_property_names()
	{
		return array();
	}
	
	function get_allowed_types()
	{
		return array('open_question', 'hotspot_question', 'fill_in_blanks_question', 'multiple_choice_question', 
					 'matching_question', 'select_question', 'matrix_question');
	}
}
?>