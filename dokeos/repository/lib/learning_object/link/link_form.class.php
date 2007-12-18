<?php
/**
 * @package repository.learningobject
 * @subpackage link
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/link.class.php';
class LinkForm extends LearningObjectForm
{
	const TOTAL_PROPERTIES = 3;

	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->add_textfield(Link :: PROPERTY_URL, get_lang('URL'), true,'size="40" style="width: 100%;"');
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->add_textfield(Link :: PROPERTY_URL, get_lang('URL'), true,'size="40" style="width: 100%;"');
	}

	function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset($lo))
		{
			$defaults[Link :: PROPERTY_URL] = $lo->get_url();
		}
		else
		{
			$defaults[Link :: PROPERTY_URL] = 'http://';
		}
		parent :: setDefaults($defaults);
	}

	function setCsvValues($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		$defaults[Link :: PROPERTY_URL] = $valuearray[3];
		parent :: setValues($defaults);			
	}

	function create_learning_object()
	{
		$object = new Link();
		$object->set_url($this->exportValue(Link :: PROPERTY_URL));
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$object->set_url($this->exportValue(Link :: PROPERTY_URL));
		return parent :: update_learning_object();
	}

	function validatecsv($value)
	{
		return parent :: validatecsv($value);
	}

}
?>
