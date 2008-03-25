<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a calendar_event
 * @author Sven Vanpoucke
 */
abstract class ImportCalendarEvent extends Import
{
	abstract function is_valid_calendar_event($array);
	abstract function convert_to_new_calendar_event($array);
	abstract static function get_all($array);
}
?>
