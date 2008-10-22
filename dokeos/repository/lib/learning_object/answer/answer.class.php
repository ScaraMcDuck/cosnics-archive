<?php

/**
 * @package repository.learningobject
 * @subpackage answer
 */

require_once dirname(__FILE__) . '/../../learning_object.class.php';

class Answer extends LearningObject
{
	function get_allowed_types() 
	{
		return array('answer');
	}
}

?>