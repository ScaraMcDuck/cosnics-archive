<?php
/**
 * @package repository.learningobject
 * @subpackage profile
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/profile.class.php';

class ProfileForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->add_html_editor(Profile :: PROPERTY_COMPETENCES, get_lang('Competences'));
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->add_html_editor(Profile :: PROPERTY_COMPETENCES, get_lang('Competences'));
	}
	function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset($lo))
		{
			$defaults[Profile :: PROPERTY_COMPETENCES] = $lo->get_competences();
		}
		
		parent :: setDefaults($defaults);
	}
	function create_learning_object()
	{
		$object = new Profile();
		$object->set_competences($this->exportValue(Profile :: PROPERTY_COMPETENCES));
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$object->set_competences($this->exportValue(Profile :: PROPERTY_COMPETENCES));
		return parent :: update_learning_object();
	}
}
?>