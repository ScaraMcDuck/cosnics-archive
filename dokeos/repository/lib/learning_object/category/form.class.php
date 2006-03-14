<?php
/**
 * @package learningobject.category
 */
require_once dirname(__FILE__) . '/../../learningobject_form.class.php';
class CategoryForm extends LearningObjectForm
{
	public function CategoryForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
	public function build_create_form()
	{
		parent :: build_create_form();
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
		$object = new Category();
		$object->set_owner_id($owner);
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_category_id($values['category']);
		$object->create();
		return $object;
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