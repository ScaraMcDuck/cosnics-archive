<?php
require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
class LearningPathForm extends LearningObjectForm
{
	public function LearningPathForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
	function build_creation_form($default_learning_object = null)
	{
		parent :: build_creation_form($default_learning_object);
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
		$learningPath = new LearningPath();
		$learningPath->set_owner_id($owner);
		$learningPath->set_title($values['title']);
		$learningPath->set_description($values['description']);
		$learningPath->create();
		return $learningPath;
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