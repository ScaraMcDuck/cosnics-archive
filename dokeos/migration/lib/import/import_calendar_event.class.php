<?php

/**
 * @package migration
 */

abstract class Import_Calendar_Event extends Import
{
	abstract function convertToNewCalendar_Event();
	abstract static function GetAllCalendar_Events();
}
?>
