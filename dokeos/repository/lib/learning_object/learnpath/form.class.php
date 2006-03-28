<?php
require_once dirname(__FILE__) . '/../../learningobject_form.class.php';
class LearnPathForm extends LearningObjectForm
{
	public function LearnPathForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
	public function build_creation_form()
	{
		parent :: build_creation_form();
		$this->add_submit_button();
	}
	public function build_modification_form($object)
	{
		parent :: build_modification_form($object);
		$this->setDefaults();
		$this->add_submit_button();
	}
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$dataManager = RepositoryDataManager::get_instance();
		$learnPath = new LearnPath();
		$learnPath->set_owner_id($owner);
		$learnPath->set_title($values['title']);
		$learnPath->set_description($values['description']);
		$learnPath->create();
		return $learnPath;
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