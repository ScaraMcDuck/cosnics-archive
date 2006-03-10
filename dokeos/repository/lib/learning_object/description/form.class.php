<?php
require_once dirname(__FILE__) . '/../../learningobject_form.class.php';
class DescriptionForm extends LearningObjectForm
{
	public function DescriptionForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
	public function build_create_form($type)
	{
		parent :: build_create_form($type);
		$this->add_submit_button();
	}
	public function build_edit_form($object)
	{
		parent :: build_edit_form($object);
		$this->setDefaults();
		$this->add_submit_button();
	}
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$dataManager = RepositoryDataManager::get_instance();
		$description = new Description();
		$description->set_owner_id($owner);
		$description->set_title($values['title']);
		$description->set_description($values['description']);
		$description->set_category_id($values['category']);
		$description->create();
		return $description;
	}
	public function update_learning_object(& $object)
	{
		$values = $this->exportValues();
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_category_id($values['category']);
		$object->update();
	}
}
?>