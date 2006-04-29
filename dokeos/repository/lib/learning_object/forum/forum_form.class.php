<?php
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/forum.class.php';
/**
 * @package repository.learningobject
 * @subpackage forum
 */
class ForumForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->add_footer();
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->setDefaults();
		$this->add_footer();
	}
	function create_learning_object()
	{
		$object = new Forum();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>