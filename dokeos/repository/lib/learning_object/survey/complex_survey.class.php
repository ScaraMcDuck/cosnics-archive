<?php
/**
 * @package repository.learningobject
 * @subpackage assessment
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';
/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexSurvey extends ComplexLearningObjectItem
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
		return array('question');
	}
}
?>