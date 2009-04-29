<?php
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPath extends LearningObject
{
	function get_allowed_types()
	{
		return array('learning_path', 'learning_path_item');
	}
	
	const PROPERTY_CONTROL_MODE = 'control_mode';
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_CONTROL_MODE);
	}
	
	function get_control_mode()
	{
		return unserialize($this->get_additional_property(self :: PROPERTY_CONTROL_MODE));
	}
	
	function set_control_mode($control_mode)
	{
		if(!is_array($control_mode))
			$control_mode = array($control_mode);
			
		$this->set_additional_property(self :: PROPERTY_CONTROL_MODE, serialize($control_mode));
	}
}
?>