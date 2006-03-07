<?php
require_once dirname(__FILE__) . '/../../learningobject_form.class.php';
class LearnpathChapterForm extends LearningObjectForm
{
	public function LearnpathChapterForm($formName, $method = 'post', $action = null)
	{
		parent :: LearningObjectForm($formName, $method, $action);
	}
	public function build_create_form()
	{
		parent :: build_create_form();
		$this->addElement('text', 'display_order', 'Display order');
		$this->addSubmitButton();
	}
	public function build_edit_form($object)
	{
		parent :: build_edit_form($object);
		$this->setDefaults();
		$this->addElement('text', 'display_order', 'Display order');
		$this->addSubmitButton();
	}
	public function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults['display_order'] = $lo->get_display_order();
		}
		parent :: setDefaults($defaults);
	}
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$dataManager = DataManager::get_instance();
		$learnpathChapter = new LearnpathChapter();
		$learnpathChapter->set_owner_id($owner);
		$learnpathChapter->set_title($values['title']);
		$learnpathChapter->set_description($values['description']);
		$learnpathChapter->set_display_order($values['display_order']);
		$learnpathChapter->create();
		return $learnpathChapter;
	}
	public function update_learning_object(& $object)
	{
		$values = $this->exportValues();
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_display_order($values['display_order']);
		$object->update();
	}
}
?>