<?php
/**
 * $Id: physical_location.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage physical_location
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents an physical_location
 */
class PhysicalLocation extends LearningObject
{
	const PROPERTY_LOCATION = 'location';

	function get_location ()
	{
		return $this->get_additional_property(self :: PROPERTY_LOCATION);
	}
	function set_location ($location)
	{
		return $this->set_additional_property(self :: PROPERTY_LOCATION, $location);
	}
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_LOCATION);
	}
}
?>