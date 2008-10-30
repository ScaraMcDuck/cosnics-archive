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
	const PROPERTY_ASSESSMENT_TYPE = 'assessment_type';
	
	const TYPE_EXERCISE = 'exercise';
	const TYPE_SURVEY = 'survey';
	const TYPE_ASSIGNMENT = 'assignment';
	
	static function get_additional_property_names()
	{
		return array(
		self :: PROPERTY_ASSESSMENT_TYPE
		);
	}
	
	function get_assessment_type()
	{
		return $this->get_additional_property(self :: PROPERTY_ASSESSMENT_TYPE);
	}
	
	function set_assessment_type($type)
	{
		$this->set_additional_property(self :: PROPERTY_ASSESSMENT_TYPE, $type);
	}
	
	function get_allowed_types()
	{
		return array('question');
	}
	
	function get_types()
	{
		return array(
		self :: TYPE_EXERCISE,
		self :: TYPE_SURVEY,
		self :: TYPE_ASSIGNMENT
		);
	}
}
?>