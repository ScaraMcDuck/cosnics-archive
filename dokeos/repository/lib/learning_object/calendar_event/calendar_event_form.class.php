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
	const PARAM_REPEAT = 'repeated';
	// Inherited
    protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->add_timewindow(CalendarEvent :: PROPERTY_START_DATE, CalendarEvent :: PROPERTY_END_DATE, Translation :: get('StartTimeWindow'), Translation :: get('EndTimeWindow'));
    	
    	
		$choices[] = $this->createElement('radio', self :: PARAM_REPEAT, '',Translation :: get('No'),0,array ('onclick' => 'javascript:timewindow_hide(\'repeat_timewindow\')', 'id' => self :: PARAM_REPEAT));
		$choices[] = $this->createElement('radio', self :: PARAM_REPEAT, '',Translation :: get('Yes'),1,array ('onclick' => 'javascript:timewindow_show(\'repeat_timewindow\')'));
		$this->addGroup($choices,null,Translation :: get('Repeat'),'<br />',false);
		$this->addElement('html','<div style="padding-left: 25px; display: block;" id="repeat_timewindow">');
		
		$options = CalendarEvent :: get_repeat_options();
		
		$this->addElement('select', CalendarEvent :: PROPERTY_REPEAT, null, $options);
		
		$this->add_timewindow(CalendarEvent :: PROPERTY_REPEAT_FROM, CalendarEvent :: PROPERTY_REPEAT_TO, Translation :: get('From'), Translation :: get('Until'));
		$this->addElement('html','</div>');
		$this->addElement('html',"<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('". self :: PARAM_REPEAT ."');
					if (expiration.checked)
					{
						timewindow_hide('repeat_timewindow');
					}
					function timewindow_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function timewindow_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");
    }
    // Inherited
    protected function build_editing_form()
    {
		parent :: build_editing_form();
    	$this->add_timewindow(CalendarEvent :: PROPERTY_START_DATE, CalendarEvent :: PROPERTY_END_DATE, Translation :: get('StartTimeWindow'), Translation :: get('EndTimeWindow'));
    	
		$choices[] = $this->createElement('radio', self :: PARAM_REPEAT, '',Translation :: get('No'),0,array ('onclick' => 'javascript:timewindow_hide(\'repeat_timewindow\')', 'id' => self :: PARAM_REPEAT));
		$choices[] = $this->createElement('radio', self :: PARAM_REPEAT, '',Translation :: get('Yes'),1,array ('onclick' => 'javascript:timewindow_show(\'repeat_timewindow\')'));
		$this->addGroup($choices,null,Translation :: get('Repeat'),'<br />',false);
		$this->addElement('html','<div style="padding-left: 25px; display: block;" id="repeat_timewindow">');
		
		$options = array();
		$options['1'] = Translation :: get('Daily');
		$options['2'] = Translation :: get('Weekly');
		$options['3'] = Translation :: get('Weekdays');
		$options['4'] = Translation :: get('BiWeekly');
		$options['5'] = Translation :: get('Monthly');
		$options['6'] = Translation :: get('Yearly');
		
		$this->addElement('select', CalendarEvent :: PROPERTY_REPEAT, null, $options);
		
		$this->add_timewindow(CalendarEvent :: PROPERTY_REPEAT_FROM, CalendarEvent :: PROPERTY_REPEAT_TO, Translation :: get('From'), Translation :: get('Until'));
		$this->addElement('html','</div>');
		$this->addElement('html',"<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('". self :: PARAM_REPEAT ."');
					if (expiration.checked)
					{
						timewindow_hide('repeat_timewindow');
					}
					function timewindow_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function timewindow_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");
	}
	// Inherited
	function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults[CalendarEvent :: PROPERTY_START_DATE] = $lo->get_start_date();
			$defaults[CalendarEvent :: PROPERTY_END_DATE] = $lo->get_end_date();
			
			if ($this->form_type == self :: TYPE_EDIT)
			{
				$repeats = $lo->repeats();
				if (!$repeats)
				{
					$defaults[self :: PARAM_REPEAT] = 0;
				}
				else
				{
					$defaults[self :: PARAM_REPEAT] = 1;
					$defaults[CalendarEvent :: PROPERTY_REPEAT] = $lo->get_repeat();
					$defaults[CalendarEvent :: PROPERTY_REPEAT_FROM] = $lo->get_repeat_from();
					$defaults[CalendarEvent :: PROPERTY_REPEAT_TO] = $lo->get_repeat_to();					
				}
			}
			else
			{
				$defaults[self :: PARAM_REPEAT] = 0;
			}
		}
		else
		{
			$defaults[self :: PARAM_REPEAT] = 0;
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
		
		if ($values[self :: PARAM_REPEAT] == 0)
		{
			$object->set_repeat(0);
			$object->set_repeat_from(0);
			$object->set_repeat_to(0);
		}
		else
		{
			$object->set_repeat($values[CalendarEvent :: PROPERTY_REPEAT]);
			$from_date = DokeosUtilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_REPEAT_FROM]);
			$to_date = DokeosUtilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_REPEAT_TO]);
			$object->set_repeat_from($from_date);
			$object->set_repeat_to($to_date);
		}
			
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
		
		if ($values[self :: PARAM_REPEAT] == 0)
		{
			$object->set_repeat(0);
			$object->set_repeat_from(0);
			$object->set_repeat_to(0);
		}
		else
		{
			$object->set_repeat($values[CalendarEvent :: PROPERTY_REPEAT]);
			$from_date = DokeosUtilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_REPEAT_FROM]);
			$to_date = DokeosUtilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_REPEAT_TO]);
			$object->set_repeat_from($from_date);
			$object->set_repeat_to($to_date);
		}
		
		return parent :: update_learning_object();
	}
}
?>
