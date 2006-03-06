<?php 
require_once dirname(__FILE__) . '/../../learningobject_form.class.php'; 

class CalendarEventForm extends LearningObjectForm 
{ 
    public function CalendarEventForm($formName, $method='post', $action=null) 
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
		$calendarEvent = new CalendarEvent();
		$calendarEvent->set_owner_id($owner);
		$calendarEvent->set_title($values['title']);
		$calendarEvent->set_description($values['description']);
		$calendarEvent->create();
		return $calendarEvent;
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