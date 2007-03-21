<?php
/**
 * @package repository.learningobject
 * @subpackage calendarevent
 */
/**
 * This class can be used to get the difference between calendar events
 */
class CalendarEventDifference extends LearningObjectDifference
{
	function get_difference()
	{
		$date_format = get_lang('dateTimeFormatLong');
		
		$object = $this->get_object();
		$version = $this->get_version();
		
		$object_string = htmlentities(get_lang('From').' '.format_locale_date($date_format,$object->get_start_date()).' '.get_lang('Until').' '.format_locale_date($date_format,$object->get_end_date()));
        $object_string = explode("\n", strip_tags($object_string));
           	
        $version_string = htmlentities(get_lang('From').' '.format_locale_date($date_format,$version->get_start_date()).' '.get_lang('Until').' '.format_locale_date($date_format,$version->get_end_date()));
		$version_string = explode("\n", strip_tags($version_string));
		
		$td = new Difference_Engine($object_string, $version_string);
		
		return array_merge($td->getDiff(), parent :: get_difference());
	}
}
?>