<?php
/**
 * @package learningobject.announcement
 */
class AnnouncementForm extends LearningObjectForm
{
	public function AnnouncementForm($formName, $method = 'post', $action = null)
	{
		parent :: LearningObjectForm($formName, $method, $action);
	}
	public function build_create_form()
	{
		parent :: build_create_form();
		$this->addSubmitButton();
	}
	public function build_edit_form($object)
	{
		parent :: build_edit_form($object);
		$this->setDefaults();
		$this->addSubmitButton();
	}
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$dataManager = DataManager::get_instance();
		$announcement = new Announcement();
		$announcement->set_owner_id($owner);
		$announcement->set_title($values['title']);
		$announcement->set_description($values['description']);
		$announcement->create();
		return $announcement;
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