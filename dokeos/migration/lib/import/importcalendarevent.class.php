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
	abstract function is_valid_calendar_event();
	abstract function convert_to_new_calendar_event();
	abstract static function get_all_calendar_events($mgdm);
}
?>
