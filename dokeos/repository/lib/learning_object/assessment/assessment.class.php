<?php
/**
 * $Id: announcement.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage assessment
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents an assessment
 */
class Assessment extends LearningObject
{
	const PROPERTY_TYPE = 'type';
	
	static function get_additional_property_names()
	{
		return array(
		self :: PROPERTY_TYPE
		);
	}
	
	function get_type()
	{
		return $this->get_additional_property(self :: PROPERTY_TYPE);
	}
	
	function set_type($type)
	{
		$this->set_additional_property($type);
	}
	
	function get_allowed_types()
	{
		return array('question');
	}
}
?>