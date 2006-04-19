<?php
require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
require_once dirname(__FILE__) . '/../../repositoryutilities.class.php';
require_once dirname(__FILE__) . '/calendar_event.class.php';
/**
 * @package repository.learningobject.calendar_event
 */
class CalendarEventForm extends LearningObjectForm
{
    public function build_creation_form($default_learning_object = null)
    {
    	parent :: build_creation_form($default_learning_object);
    	$this->add_timewindow(CalendarEvent :: PROPERTY_START_DATE, CalendarEvent :: PROPERTY_END_DATE, get_lang('StartTimeWindow'), get_lang('EndTimeWindow'));
    	$this->setDefaults();
    	$this->add_submit_button();
    }
    public function build_editing_form($object)
    {
		parent :: build_editing_form($object);
    	$this->add_timewindow(CalendarEvent :: PROPERTY_START_DATE, CalendarEvent :: PROPERTY_END_DATE, get_lang('StartTimeWindow'), get_lang('EndTimeWindow'));
		$this->setDefaults();
		$this->add_submit_button();
	}
	public function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults[CalendarEvent :: PROPERTY_START_DATE] = $lo->get_start_date();
			$defaults[CalendarEvent :: PROPERTY_END_DATE] = $lo->get_end_date();
		}
		parent :: setDefaults($defaults);
	}

	function create_learning_object($owner)
	{
		$object = new CalendarEvent();
		$object->set_start_date($this->exportValue(RepositoryUtilities :: time_from_datepicker(CalendarEvent :: PROPERTY_START_DATE)));
		$object->set_end_date($this->exportValue(RepositoryUtilities :: time_from_datepicker(CalendarEvent :: PROPERTY_END_DATE)));
		$this->set_learning_object($object);
		return parent :: create_learning_object($owner);
	}
	function update_learning_object(& $object)
	{
		$object->set_start_date($this->exportValue(RepositoryUtilities :: time_from_datepicker(CalendarEvent :: PROPERTY_START_DATE)));
		$object->set_end_date($this->exportValue(RepositoryUtilities :: time_from_datepicker(CalendarEvent :: PROPERTY_END_DATE)));
		return parent :: update_learning_object(& $object);
	}
}
?>