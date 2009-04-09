<?php
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPathItem extends LearningObject
{
	const PROPERTY_REFERENCE = 'reference';
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_REFERENCE);
	}
	
	function get_reference()
	{
		return $this->get_additional_property(self :: PROPERTY_REFERENCE);
	}
	
	function set_reference($reference)
	{
		$this->set_additional_property(self :: PROPERTY_REFERENCE, $reference);
	}
}
?>