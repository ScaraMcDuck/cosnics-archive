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
	
	const PROPERTY_TIMES_TAKEN = 'times_taken';
	const PROPERTY_AVERAGE_SCORE = 'average_score';
	const PROPERTY_MAXIMUM_SCORE = 'maximum_score';
	
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
	
	function get_times_taken() 
	{
		return WeblcmsDataManager :: get_instance()->get_num_user_assessments($this);
	}
	
	function get_average_score()
	{
		return WeblcmsDataManager :: get_instance()->get_average_score($this);
	}
	
	function get_maximum_score()
	{
		return WeblcmsDataManager :: get_instance()->get_maximum_score($this);
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