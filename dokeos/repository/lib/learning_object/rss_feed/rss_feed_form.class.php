<?php
/**
 * @package repository.object
 * @subpackage link
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/rss_feed.class.php';
class RssFeedForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->add_textfield(RssFeed :: PROPERTY_URL, Translation :: get('URL'), true,'size="40" style="width: 100%;"');
		$this->addElement('category');
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->add_textfield(RssFeed :: PROPERTY_URL, Translation :: get('URL'), true,'size="40" style="width: 100%;"');
		$this->addElement('category');
	}
	function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset($lo))
		{
			$defaults[RssFeed :: PROPERTY_URL] = $lo->get_url();
		}
		else
		{
			$defaults[RssFeed :: PROPERTY_URL] = 'http://';
		}
		parent :: setDefaults($defaults);
	}
	function create_learning_object()
	{
		$learning_object = new RssFeed();
		$learning_object->set_url($this->exportValue(RssFeed :: PROPERTY_URL));
		$this->set_learning_object($learning_object);
		return parent :: create_learning_object();
	}
	function update_learning_object()
	{
		$learning_object = $this->get_learning_object();
		$learning_object->set_url($this->exportValue(RssFeed :: PROPERTY_URL));
		return parent :: update_learning_object();
	}
}
?>