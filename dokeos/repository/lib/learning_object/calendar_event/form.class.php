<?php
require_once dirname(__FILE__) . '/../../learningobject_form.class.php';
require_once dirname(__FILE__) . '/../../repositoryutilities.class.php';

class CalendarEventForm extends LearningObjectForm
{
    public function CalendarEventForm($formName, $method='post', $action=null)
    {
    	parent :: LearningObjectForm($formName, $method, $action);
    }
    public function build_create_form()
    {
    	parent :: build_create_form();
    	$this->addElement('datepicker', 'start', get_lang('StartTimeWindow'), array ('form_name' => $this->getAttribute('id')));
		$this->addRule('start', get_lang('InvalidDate'), 'date');
		$this->addElement('datepicker', 'end', get_lang('EndTimeWindow'), array ('form_name' => $this->getAttribute('id')));
		$this->addRule('end', get_lang('InvalidDate'), 'date');
		$this->addRule(array ('start', 'end'), get_lang('StartDateShouldBeBeforeEndDate'), 'date_compare', 'lte');
    	$this->add_submit_button();
    }
    public function build_edit_form($object)
    {
		parent :: build_edit_form($object);
		$this->addElement('datepicker', 'start', get_lang('StartTimeWindow'), array ('form_name' => $this->getAttribute('id')));
		$this->addRule('start', get_lang('InvalidDate'), 'date');
		$this->addElement('datepicker', 'end', get_lang('EndTimeWindow'), array ('form_name' => $this->getAttribute('id')));
		$this->addRule('end', get_lang('InvalidDate'), 'date');
		$this->addRule(array ('start', 'end'), get_lang('StartDateShouldBeBeforeEndDate'), 'date_compare', 'lte');
		$this->setDefaults();
		$this->add_submit_button();
	}
	public function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults['start'] = $lo->get_start_date();
			$defaults['end'] = $lo->get_end_date();
		}
		parent :: setDefaults($defaults);
	}
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$dataManager = RepositoryDataManager::get_instance();
		$calendarEvent = new CalendarEvent();
		$calendarEvent->set_owner_id($owner);
		$calendarEvent->set_title($values['title']);
		$calendarEvent->set_description($values['description']);
		$calendarEvent->set_parent_id($values['category']);
		$calendarEvent->set_start_date(RepositoryUtilities :: date_from_datepicker($values['start']));
		$calendarEvent->set_end_date(RepositoryUtilities :: date_from_datepicker($values['end']));
		$calendarEvent->create();
		return $calendarEvent;
	}
	public function update_learning_object(& $object)
	{
		$values = $this->exportValues();
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_parent_id($values['category']);
		$object->set_start_date(RepositoryUtilities :: date_from_datepicker($values['start']));
		$object->set_end_date(RepositoryUtilities :: date_from_datepicker($values['end']));
		$object->update();
	}
}
?>