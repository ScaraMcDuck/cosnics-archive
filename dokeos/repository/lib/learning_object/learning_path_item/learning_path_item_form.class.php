<?php
require_once dirname(__FILE__) . '/../../learning_object_form.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPathItemForm extends LearningObjectForm
{
	function create_learning_object()
	{
		$object = new LearningPathItem();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>