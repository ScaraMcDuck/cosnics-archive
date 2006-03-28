<?php
/**
 * @package learningobject.link
 */
require_once dirname(__FILE__) . '/../../learningobject_form.class.php';
class LinkForm extends LearningObjectForm
{
	function LinkForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
	function build_creation_form()
	{
		parent :: build_creation_form('link', true);
		$this->add_textfield('url', 'URL',true,'size="50"');
		$this->add_submit_button();
	}
	function build_editing_form($object)
	{
		parent :: build_editing_form($object);
		$this->add_textfield('url', 'URL',true,'size="50"');
		$this->setDefaults();
		$this->add_submit_button();
	}
	function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults['url'] = $lo->get_url();
		}
		parent :: setDefaults($defaults);
	}
	function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$dataManager = RepositoryDataManager::get_instance();
		$link = new Link();
		$link->set_owner_id($owner);
		$link->set_title($values['title']);
		$link->set_description($values['description']);
		$link->set_url($values['url']);
		$link->set_parent_id($values['category']);
		$link->create();
		return $link;
	}
	function update_learning_object(& $object)
	{
		$values = $this->exportValues();
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_url($values['url']);
		$object->set_parent_id($values['category']);
		$object->update();
	}
}
?>