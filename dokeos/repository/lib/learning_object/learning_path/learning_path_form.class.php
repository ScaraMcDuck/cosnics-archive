<?php
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/learning_path.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPathForm extends LearningObjectForm
{
	function create_learning_object()
	{
		$object = new LearningPath();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>