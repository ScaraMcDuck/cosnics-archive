<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a course
 * @author Sven Vanpoucke
 */
abstract class ImportCourse extends Import
{
	abstract function is_valid_course();
	abstract function convert_to_new_course();
	abstract static function get_all_courses();
}
?>
