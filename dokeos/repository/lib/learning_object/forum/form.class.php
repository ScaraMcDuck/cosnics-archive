<?php
require_once dirname(__FILE__) . '/../../learningobject_form.class.php';
class ForumForm extends LearningObjectForm
{
	public function ForumForm($formName, $method = 'post', $action = null)
	{
		parent :: LearningObjectForm($formName, $method, $action);
	}
	public function build_create_form()
	{
		parent :: build_create_form();
		$this->addElement('text', 'forum_type', 'Forum type');
		$this->addSubmitButton();
	}
	public function build_edit_form($object)
	{
		parent :: build_edit_form($object);
		$this->addElement('text', 'forum_type', 'Forum type');
		$this->setDefaults();
		$this->addSubmitButton();
	}
	public function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults['forum_type'] = $lo->get_forum_type();
		}
		parent :: setDefaults($defaults);
	}
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$dataManager = DataManager::get_instance();
		$forum = new Forum();
		$forum->set_owner_id($owner);
		$forum->set_title($values['title']);
		$forum->set_description($values['description']);
		$forum->set_forum_type($values['forum_type']);
		$forum->create();
		return $forum;
	}
	public function update_learning_object(& $object)
	{
		$values = $this->exportValues();
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_forum_type($values['forum_type']);
		$object->update();
	}
}
?>