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
class Survey extends LearningObject
{
	const PROPERTY_ASSESSMENT_TYPE = 'assessment_type';
	
	const TYPE_SURVEY = 'survey';
	
	const PROPERTY_TIMES_TAKEN = 'times_taken';
	const PROPERTY_AVERAGE_SCORE = 'average_score';
	const PROPERTY_MAXIMUM_SCORE = 'maximum_score';
	const PROPERTY_MAXIMUM_TIMES_TAKEN = 'max_times_taken';
	const PROPERTY_FINISH_TEXT = 'finish_text';
	const PROPERTY_ANONYMOUS = 'anonymous';
	
	static function get_additional_property_names()
	{
		return array(
			self :: PROPERTY_ASSESSMENT_TYPE,
			self :: PROPERTY_MAXIMUM_TIMES_TAKEN,
			self :: PROPERTY_FINISH_TEXT,
			self :: PROPERTY_ANONYMOUS
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
	
	function get_maximum_times_taken()
	{
		return $this->get_additional_property(self :: PROPERTY_MAXIMUM_TIMES_TAKEN);
	}
	
	function set_maximum_times_taken($value)
	{
		$this->set_additional_property(self :: PROPERTY_MAXIMUM_TIMES_TAKEN, $value);
	}
	
	function get_finish_text()
	{
		return $this->get_additional_property(self :: PROPERTY_FINISH_TEXT);
	}
	
	function set_finish_text($value)
	{
		$this->set_additional_property(self :: PROPERTY_FINISH_TEXT, $value);
	}
	
	function get_anonymous()
	{
		return $this->get_additional_property(self :: PROPERTY_ANONYMOUS);
	}

	function set_anonymous($value)
	{
		return $this->set_additional_property(self :: PROPERTY_ANONYMOUS, $value);
	}
	
	function get_allowed_types()
	{
		return array('question');
	}
	
	function get_times_taken() 
	{
		return WeblcmsDataManager :: get_instance()->get_num_user_assessments($this);
	}
	
	function get_table()
	{
		return 'survey';
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
			self :: TYPE_SURVEY
		);
	}
}
?>