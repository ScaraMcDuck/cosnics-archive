<?php
/**
 * @package repository.learningobject
 * @subpackage feedback
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/feedback.class.php';
/**
 * A form to create/update a feedback
 */
class FeedbackForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('select',Feedback :: PROPERTY_ICON,Translation :: get('icon'),Feedback::get_possible_icons());
		$this->addElement('category');
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('select',Feedback :: PROPERTY_ICON,Translation :: get('icon'),Feedback::get_possible_icons());
		$this->addElement('category');
	}
	function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset($lo))
		{
			$defaults[Feedback :: PROPERTY_ICON] = $lo->get_icon();
		}
		parent :: setDefaults($defaults);
	}
	function create_learning_object()
	{
		$object = new Feedback();
		$object->set_icon($this->exportValue(Feedback :: PROPERTY_ICON));
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$object->set_icon($this->exportValue(Feedback :: PROPERTY_ICON));
		return parent :: update_learning_object();
	}
}
?>