<?php
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
require_once dirname(__FILE__) . '/../../complex_content_object_item.class.php';

class ComplexLearningPathItem extends ComplexContentObjectItem
{
	const PROPERTY_PREREQUISITES = 'prerequisites';
	
	static function get_additional_property_names()
	{
		return array(self :: PROPERTY_PREREQUISITES);
	}
	
	function get_prerequisites()
	{
		return $this->get_additional_property(self :: PROPERTY_PREREQUISITES);
	}
	
	function set_prerequisites($value)
	{
		$this->set_additional_property(self :: PROPERTY_PREREQUISITES, $value);
	}
}
?>