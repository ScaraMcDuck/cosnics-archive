<?php

/**
 * @package migration.lib.import
 */

abstract class ImportCourse extends Import
{
	abstract function convert_to_new_course();
	abstract static function get_all_courses();
}
?>
