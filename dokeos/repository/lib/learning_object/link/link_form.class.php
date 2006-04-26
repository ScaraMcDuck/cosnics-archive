<?php
/**
 * @package repository.learningobject.link
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/link.class.php';
class LinkForm extends LearningObjectForm
{
	function build_creation_form($default_learning_object = null)
	{
		parent :: build_creation_form($default_learning_object);
		$this->add_textfield(Link :: PROPERTY_URL, get_lang('URL'), true,'size="100"');
		$this->setDefaults();
		$this->add_footer();
	}
	function build_editing_form($object)
	{
		parent :: build_editing_form($object);
		$this->add_textfield(Link :: PROPERTY_URL, get_lang('URL'), true,'size="100"');
		$this->setDefaults();
		$this->add_footer();
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
	function create_learning_object($owner)
	{
		$object = new Link();
		$object->set_url($this->exportValue(Link :: PROPERTY_URL));
		$this->set_learning_object($object);
		return parent :: create_learning_object($owner);
	}
	function update_learning_object($object)
	{
		$object->set_url($this->exportValue(Link :: PROPERTY_URL));
		return parent :: update_learning_object($object);
	}
}
?>