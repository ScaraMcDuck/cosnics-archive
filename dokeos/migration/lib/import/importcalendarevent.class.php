<?php

/**
 * @package migration.lib.import
 */

abstract class ImportCalendarEvent extends Import
{
	abstract function convert_to_new_calendar_event();
	abstract static function get_all_calendar_events();
}
?>
