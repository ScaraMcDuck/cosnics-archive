<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage calendar_event
 */
require_once dirname(__FILE__) . '/../../learning_object_form.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__) . '/calendar_event.class.php';
/**
 * This class represents a form to create or update calendar events
 */
class CalendarEventForm extends LearningObjectForm
{
	const TOTAL_PROPERTIES = 4;
	// Inherited
    protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->add_timewindow(CalendarEvent :: PROPERTY_START_DATE, CalendarEvent :: PROPERTY_END_DATE, Translation :: get('StartTimeWindow'), Translation :: get('EndTimeWindow'));
    }
    // Inherited
    protected function build_editing_form()
    {
		parent :: build_editing_form();
    	$this->add_timewindow(CalendarEvent :: PROPERTY_START_DATE, CalendarEvent :: PROPERTY_END_DATE, Translation :: get('StartTimeWindow'), Translation :: get('EndTimeWindow'));
	}
	// Inherited
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

	function set_csv_values($valuearray)
	{	
		

		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		$defaults[CalendarEvent :: PROPERTY_START_DATE] = $valuearray[3];
		$defaults[CalendarEvent :: PROPERTY_END_DATE] = $valuearray[4];
		parent :: set_values($defaults);
	}

	// Inherited
	function create_learning_object()
	{
		$object = new CalendarEvent();
		$values = $this->exportValues();
		$object->set_start_date(DokeosUtilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_START_DATE]));
		$object->set_end_date(DokeosUtilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_END_DATE]));
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	// Inherited
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
		$object->set_start_date(DokeosUtilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_START_DATE]));
		$object->set_end_date(DokeosUtilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_END_DATE]));
		return parent :: update_learning_object();
	}
}
?>
