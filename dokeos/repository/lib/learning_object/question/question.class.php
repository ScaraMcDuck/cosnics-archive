<?php

/**
 * @package repository.learningobject
 * @subpackage question
 */


require_once dirname(__FILE__) . '/../../learning_object.class.php';
class Question extends LearningObject
{
	const TYPE_OPEN = 'open';
	const TYPE_DOCUMENT = 'document';
	const TYPE_OPEN_WITH_DOCUMENT = 'open with document';
	const TYPE_MATCHING = 'matching';
	const TYPE_FILL_IN_BLANKS = 'fill in blanks';
	const TYPE_MULTIPLE_CHOICE = 'multiple choice';
	const TYPE_MULTIPLE_ANSWER = 'multiple answer';
	const TYPE_YES_NO = 'yes/no';
	const TYPE_PERCENTAGE = 'percentage rating';
	const TYPE_SCORE = 'point rating';
	
	const PROPERTY_QUESTION_TYPE = 'question_type';
	
	function get_question_type()
	{
		return $this->get_additional_property(self :: PROPERTY_QUESTION_TYPE);
	}
	
	function set_question_type($question_type) 
	{
		$this->set_additional_property(self :: PROPERTY_QUESTION_TYPE, $question_type);
	}
	
	static function get_additional_property_names() 
	{
		return array(self :: PROPERTY_QUESTION_TYPE);
	}
	
	function get_allowed_types()
	{
		return array('answer');
	}
	
	static function get_question_types()
	{
		return array(
		self :: TYPE_OPEN,
		self :: TYPE_DOCUMENT,
		self :: TYPE_OPEN_WITH_DOCUMENT,
		self :: TYPE_MATCHING,
		self :: TYPE_FILL_IN_BLANKS,
		self :: TYPE_MULTIPLE_CHOICE,
		self :: TYPE_MULTIPLE_ANSWER,
		self :: TYPE_YES_NO,
		self :: TYPE_PERCENTAGE,
		self :: TYPE_SCORE
		);
	}
}

?>