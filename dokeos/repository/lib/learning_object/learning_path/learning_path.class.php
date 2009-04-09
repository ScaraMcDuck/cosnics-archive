<?php
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPath extends LearningObject
{
	function get_allowed_types()
	{
		return array('learning_path', 'learning_path_item');
	}
}
?>