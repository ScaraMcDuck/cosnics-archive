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
		$html = parent :: get_full_html();
		$object = $this->get_learning_object();
		$date_format = get_lang('dateTimeFormatLong');
		$append = '<div class="calendar_event_range" style="margin-top: 1em;"">'.htmlentities(get_lang('From').' '.format_locale_date($date_format,$object->get_start_date()).' '.get_lang('Until').' '.format_locale_date($date_format,$object->get_end_date())).'</div>';
		return preg_replace('|</div>\s*$|s', $append.'</div>', $html);
	}
}
?>