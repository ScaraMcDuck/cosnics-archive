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
	const TYPE_ASSIGNMENT = 'assignment';
	
	const PROPERTY_TIMES_TAKEN = 'times_taken';
	const PROPERTY_AVERAGE_SCORE = 'average_score';
	const PROPERTY_MAXIMUM_SCORE = 'maximum_score';
	const PROPERTY_MAXIMUM_ATTEMPTS = 'max_attempts';
	const PROPERTY_QUESTIONS_PER_PAGE = 'questions_per_page';
	
	static function get_additional_property_names()
	{
		return array(
		self :: PROPERTY_ASSESSMENT_TYPE,
		self :: PROPERTY_MAXIMUM_ATTEMPTS,
		self :: PROPERTY_QUESTIONS_PER_PAGE
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
	
	function get_maximum_attempts()
	{
		return $this->get_additional_property(self :: PROPERTY_MAXIMUM_ATTEMPTS);
	}
	
	function set_maximum_attempts($value)
	{
		$this->set_additional_property(self :: PROPERTY_MAXIMUM_ATTEMPTS, $value);
	}
	
	function get_questions_per_page()
	{
		return $this->get_additional_property(self :: PROPERTY_QUESTIONS_PER_PAGE);
	}
	
	function set_questions_per_page($value)
	{
		$this->set_additional_property(self :: PROPERTY_QUESTIONS_PER_PAGE, $value);
	}
	
	function get_allowed_types()
	{
		return array( 'question', 'rating_question', 'open_question', 'hotspot_question', 'fill_in_blanks_question', 'multiple_choice_question', 'matching_question');
	}
	
	function get_table()
	{
		return 'assessment';
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
		self :: TYPE_ASSIGNMENT
		);
	}
}
?>