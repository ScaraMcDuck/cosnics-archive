<?php
require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
/**
 * @package learningobject.forum
 */
class ForumForm extends LearningObjectForm
{
	public function ForumForm($formName, $method = 'post', $action = null)
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
		$forum = new Forum();
		$forum->set_owner_id($owner);
		$forum->set_title($values['title']);
		$forum->set_description($values['description']);
		$forum->set_parent_id($values['category']);
		$forum->create();
		return $forum;
	}
	public function update_learning_object(& $object)
	{
		$values = $this->exportValues();
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_parent_id($values['category']);
		$object->update();
	}
}
?>