<?php
/**
 * $Id: announcement.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents an open question
 */
class OpenQuestion extends LearningObject
{
	const PROPERTY_QUESTION_TYPE = 'question_type';
	
	const TYPE_OPEN = 1;
	const TYPE_OPEN_WITH_DOCUMENT = 2;
	const TYPE_DOCUMENT = 3;
	
	static function get_additional_property_names()
	{
		return array(
		self :: PROPERTY_QUESTION_TYPE,
		);
	}
	
	function get_question_type()
	{
		return $this->get_additional_property(self :: PROPERTY_QUESTION_TYPE);
	}
	
	function set_question_type($question_type)
	{
		$this->set_additional_property(self :: PROPERTY_QUESTION_TYPE, $question_type);
	}
	
	function get_table()
	{
		return 'open_question';
	}

	function get_types()
	{
		return array(
			self :: TYPE_OPEN => Translation :: get('OpenQuestion'),
			self :: TYPE_OPEN_WITH_DOCUMENT => Translation :: get('OpenQuestionWithDocument'),
			self :: TYPE_DOCUMENT => Translation :: get('DocumentQuestion')
		);
	}
}
?>