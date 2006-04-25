<?php
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/description.class.php';
/**
 * @package repository.learningobject.description
 */
class DescriptionForm extends LearningObjectForm
{
	function build_creation_form(& $default_learning_object = null)
	{
		parent :: build_creation_form(& $default_learning_object);
		$this->add_footer();
	}
	public function build_editing_form(& $object)
	{
		parent :: build_editing_form(& $object);
		$this->setDefaults();
		$this->add_footer();
	}
	function create_learning_object($owner)
	{
		$object = new Description();
		$this->set_learning_object(& $object);
		return parent :: create_learning_object($owner);
	}
}
?>