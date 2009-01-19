<?php
/**
 * @package repository.learningobject
 * @subpackage assessment
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';
/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexMatchingQuestion extends ComplexLearningObjectItem
{
	const PROPERTY_WEIGHT = 'weight';
	
	static function get_additional_property_names()
	{
		return array(self :: PROPERTY_WEIGHT);
	}
	
	function get_weight()
	{
		return $this->get_additional_property(self :: PROPERTY_WEIGHT);
	}
	
	function set_weight($value)
	{
		$this->set_additional_property(self :: PROPERTY_WEIGHT, $value);
	}
}
?>