<?php

/**
 * @package repository.learningobject
 * @subpackage question
 */


require_once dirname(__FILE__) . '/../../learning_object.class.php';
class Question extends LearningObject
{
	const TYPE_OPEN = 'open';
	const TYPE_MATCHING = 'matching';
	const TYPE_FILL_IN_BLANKS = 'fill in blanks';
	const TYPE_MULTIPLE_CHOICE = 'multiple choice';
	const TYPE_MULTIPLE_ANSWER = 'multiple answer';
	const TYPE_YES_NO = 'yes/no';
	const TYPE_PERCENTAGE = 'percentage rating';
	const TYPE_SCORE = 'point rating';
	
	function get_allowed_types()
	{
		return array('answer');
	}
	
	function get_question_types()
	{
		return array(
		TYPE_OPEN,
		TYPE_MATCHING,
		TYPE_FILL_IN_BLANKS,
		TYPE_MULTIPLE_CHOICE,
		TYPE_MULTIPLE_ANSWER,
		TYPE_YES_NO,
		TYPE_PERCENTAGE,
		TYPE_SCORE
		);
	}
}

?>