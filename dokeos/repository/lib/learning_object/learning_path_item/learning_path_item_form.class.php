<?php
require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
/**
 * @package repository.learningobject.learning_path
 */
class LearningPathItemForm extends LearningObjectForm
{
	public function LearningPathItemForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
	public function build_creation_form()
	{
		parent :: build_creation_form();
		$this->add_submit_button();
	}
	public function build_editing_form($object)
	{
		parent :: build_editing_form($object);
		$this->setDefaults();
		$this->add_submit_button();
	}
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$dataManager = RepositoryDataManager::get_instance();
		$learningPathItem = new LearningPathItem();
		$learningPathItem->set_owner_id($owner);
		$learningPathItem->set_title($values['title']);
		$learningPathItem->set_description($values['description']);
		$learningPathItem->create();
		return $learningPathItem;
	}
	public function update_learning_object(& $object)
	{
		$values = $this->exportValues();
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->update();
	}
}
?>