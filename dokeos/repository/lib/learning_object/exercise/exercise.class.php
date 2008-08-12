<?php
/**
 * $Id: announcement.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents an exercise
 */
class Exercise extends LearningObject
{
	function get_allowed_types()
	{
		return array('fill_in_blanks_question', 'matching_question', 'multiple_choice_question',
					 'open_question', 'exercise');
	}
}
?>