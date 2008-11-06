<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage calendar_event
 */
/**
 * This class can be used to display calendar events
 */
class CalendarEventDisplay extends LearningObjectDisplay
{
	// Inherited
	function get_full_html()
	{
		return parent :: get_full_html();
	}
	function get_description()
	{
		$description = parent::get_description();
		$object = $this->get_learning_object();
		$date_format = Translation :: get('dateTimeFormatLong');
		
		$prepend = array();

		$repeats = $object->repeats();
		
		if ($repeats)
		{
			$prepend[] = '<div class="calendar_event_range">';
			$prepend[] = Translation :: get('Repeats');
			$prepend[] = ' ';
			$prepend[] = strtolower($object->get_repeat_as_string());
			$prepend[] = ' ';
			$prepend[] = strtolower(Translation :: get('From'));
			$prepend[] = ' ';
			$prepend[] = Text :: format_locale_date($date_format,$object->get_repeat_from());
			$prepend[] = ' ';
			$prepend[] = Translation :: get('Until');
			$prepend[] = ' ';
			$prepend[] = Text :: format_locale_date($date_format,$object->get_repeat_to());
			$prepend[] = '</div>';
		}
		else
		{
			$prepend[] = '<div class="calendar_event_range">';
			$prepend[] = Translation :: get('From');
			$prepend[] = ' ';
			$prepend[] = Text :: format_locale_date($date_format,$object->get_start_date());
			$prepend[] = ' ';
			$prepend[] = Translation :: get('Until');
			$prepend[] = ' ';
			$prepend[] = Text :: format_locale_date($date_format,$object->get_end_date());
			$prepend[] = '</div>';
		}
		
		return implode('', $prepend) . $description;
	}
}
?>