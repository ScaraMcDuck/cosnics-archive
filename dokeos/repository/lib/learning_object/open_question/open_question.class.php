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
	function get_allowed_types()
	{
		return array();
	}
}
?>