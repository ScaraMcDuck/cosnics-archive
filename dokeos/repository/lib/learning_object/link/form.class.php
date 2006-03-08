<?php
/**
 * @package learningobject.announcement
 */
require_once dirname(__FILE__) . '/../../learningobject_form.class.php';
class LinkForm extends LearningObjectForm
{
	public function LinkForm($formName, $method = 'post', $action = null)
	{
		parent :: LearningObjectForm($formName, $method, $action);
	}
	public function build_create_form()
	{
		$this->addElement('text', 'url', 'Url');
		parent :: build_create_form();
		$this->addSubmitButton();
	}
	public function build_edit_form($object)
	{
		parent :: build_edit_form($object);
		$this->addElement('text', 'url', 'Url');
		$this->setDefaults();
		$this->addSubmitButton();
	}
	public function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults['url'] = $lo->get_url();
		}
		parent :: setDefaults($defaults);
	}
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$dataManager = RepositoryDataManager::get_instance();
		$link = new Link();
		$link->set_owner_id($owner);
		$link->set_title($values['title']);
		$link->set_description($values['description']);
		$link->set_url($values['url']);
		$link->create();
		return $link;
	}
	public function update_learning_object(& $object)
	{
		$values = $this->exportValues();
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_url($values['url']);
		$object->update();
	}
}
?>