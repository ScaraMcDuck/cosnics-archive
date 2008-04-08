<?php
/**
 * @package repository.learningobject
 * @subpackage calendarevent
 * 
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
 */
/**
 * This class can be used to get the difference between calendar events
 */
class CalendarEventDifference extends LearningObjectDifference
{
	function get_difference()
	{
		$date_format = Translation :: get('dateTimeFormatLong');
		
		$object = $this->get_object();
		$version = $this->get_version();
		
		$object_string = htmlentities(Translation :: get('From').' '.Text :: format_locale_date($date_format,$object->get_start_date()).' '.Translation :: get('Until').' '.Text :: format_locale_date($date_format,$object->get_end_date()));
        $object_string = explode("\n", strip_tags($object_string));
           	
        $version_string = htmlentities(Translation :: get('From').' '.Text :: format_locale_date($date_format,$version->get_start_date()).' '.Translation :: get('Until').' '.Text :: format_locale_date($date_format,$version->get_end_date()));
		$version_string = explode("\n", strip_tags($version_string));
		
		$td = new Difference_Engine($object_string, $version_string);
		
		return array_merge($td->getDiff(), parent :: get_difference());
	}
}
?>