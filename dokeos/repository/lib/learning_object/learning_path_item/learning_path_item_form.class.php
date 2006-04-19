<?php
require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
/**
 * @package repository.learningobject.learning_path
 */
class LearningPathItemForm extends LearningObjectForm
{
	function LearningPathItemForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
	function build_creation_form()
	{
		parent :: build_creation_form();
		$this->add_submit_button();
	}
	function build_editing_form($object)
	{
		parent :: build_editing_form($object);
		$this->setDefaults();
		$this->add_submit_button();
	}
	function create_learning_object($owner)
	{
		$object = new LearningPathItem();
		$this->set_learning_object(& $object);
		return parent :: create_learning_object($owner);
	}
}
?>