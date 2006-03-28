<?php
require_once dirname(__FILE__) . '/../../learningobject_form.class.php';
class ForumPostForm extends LearningObjectForm
{
	public function ForumPostForm($formName, $method = 'post', $action = null)
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
		$forumPost = new ForumPost();
		$forumPost->set_owner_id($owner);
		$forumPost->set_title($values['title']);
		$forumPost->set_description($values['description']);
		$forumPost->create();
		return $forumPost;
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