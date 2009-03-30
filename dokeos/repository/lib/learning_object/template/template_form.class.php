<?php
/**
 * $Id: template_form.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage template
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/template.class.php';
/**
 * This class represents a form to create or update templates
 */
class TemplateForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->build_default_form();
		$this->addElement('category');
	}
	
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->build_default_form();
		$this->addElement('category');
	}
	
	private function build_default_form()
	{
		$this->add_html_editor(Template :: PROPERTY_DESIGN, Translation :: get('Design'), false);

	}
	
	function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset($lo))
		{
			$defaults[Template :: PROPERTY_DESIGN] = $lo->get_design();
		}

		parent :: setDefaults($defaults);
	}

	function create_learning_object()
	{
		$values = $this->exportValues();
		
		$object = new Template();
		$object->set_design($values[Template :: PROPERTY_DESIGN]);
		parent :: set_learning_object($object);
		return parent :: create_learning_object();
	}
	
	function update_learning_object()
	{
		$values = $this->exportValues();
		
		$object = new Template();
		$object->set_design($values[Template :: PROPERTY_DESIGN]);
		parent :: set_learning_object($object);
		return parent :: update_learning_object();
	}
}
?>
