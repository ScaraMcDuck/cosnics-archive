<?php
require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
require_once dirname(__FILE__) . '/../../repositoryutilities.class.php';
require_once dirname(__FILE__) . '/calendar_event.class.php';
/**
 * @package repository.learningobject
 * @subpackage calendar_event
 */
class CalendarEventForm extends LearningObjectForm
{
    protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->add_timewindow(CalendarEvent :: PROPERTY_START_DATE, CalendarEvent :: PROPERTY_END_DATE, get_lang('StartTimeWindow'), get_lang('EndTimeWindow'));
    }
    protected function build_editing_form()
    {
		parent :: build_editing_form();
    	$this->add_timewindow(CalendarEvent :: PROPERTY_START_DATE, CalendarEvent :: PROPERTY_END_DATE, get_lang('StartTimeWindow'), get_lang('EndTimeWindow'));
	}
	function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults[CalendarEvent :: PROPERTY_START_DATE] = $lo->get_start_date();
			$defaults[CalendarEvent :: PROPERTY_END_DATE] = $lo->get_end_date();
		}
		parent :: setDefaults($defaults);
	}

	function create_learning_object()
	{
		$object = new CalendarEvent();
		$values = & $this->exportValues();
		$object->set_start_date(RepositoryUtilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_START_DATE]));
		$object->set_end_date(RepositoryUtilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_END_DATE]));
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$values = & $this->exportValues();
		$object->set_start_date(RepositoryUtilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_START_DATE]));
		$object->set_end_date(RepositoryUtilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_END_DATE]));
		return parent :: update_learning_object();
	}
}
?>