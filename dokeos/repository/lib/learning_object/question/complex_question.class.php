<?php
/**
 * @package repository.learningobject
 * @subpackage question
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';

class ComplexQuestion extends ComplexLearningObjectItem
{
	const PROPERTY_WEIGHT = 'weight';
	
	function get_weight() 
	{
		return $this->get_additional_property(self :: PROPERTY_WEIGHT);
	}
	
	function set_weight($weight)
	{
		$this->set_additional_property(self :: PROPERTY_WEIGHT, $weight);
	}
	
	static function get_additional_property_names()
	{
		return array(self :: PROPERTY_WEIGHT);
	}
	
	function get_allowed_types()
	{
		return array('answer');
	}
}
?>