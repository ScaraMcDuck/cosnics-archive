<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package repository.learningobject.learning_path
 */
class LearningPathChapter extends LearningObject
{
	function is_ordered()
	{
		return true;
	}
}
?>