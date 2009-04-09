<?php
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';

class ComplexLearningPath extends ComplexLearningObjectItem
{
	function get_allowed_types()
	{
		return array('learning_path', 'learning_path_item');
	}
}
?>