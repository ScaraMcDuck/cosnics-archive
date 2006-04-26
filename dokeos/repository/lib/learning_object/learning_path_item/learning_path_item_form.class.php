<?php
require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
/**
 * @package repository.learningobject.learning_path
 */
class LearningPathItemForm extends LearningObjectForm
{
	function build_creation_form($default_learning_object = null)
	{
		parent :: build_creation_form($default_learning_object);
		$this->add_footer();
	}
	function build_editing_form($object)
	{
		parent :: build_editing_form($object);
		$this->setDefaults();
		$this->add_footer();
	}
	function create_learning_object($owner)
	{
		$object = new LearningPathItem();
		$this->set_learning_object($object);
		return parent :: create_learning_object($owner);
	}
}
?>