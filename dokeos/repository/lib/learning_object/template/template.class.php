<?php
/**
 * $Id: template.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage template
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents an template
 */
class Template extends LearningObject
{
	const PROPERTY_DESIGN = 'design';
	
	function get_design()
	{
		return $this->get_additional_property(self :: PROPERTY_DESIGN);
	}

	function set_design($design)
	{
		return $this->set_additional_property(self :: PROPERTY_DESIGN, $design);
	}
	
	function is_versionable()
	{
		return false;
	}
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_DESIGN);
	}
}
?>