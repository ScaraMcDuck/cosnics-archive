<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a course category
 * @author Sven Vanpoucke
 */
abstract class ImportCourseCategory extends Import
{
	abstract function is_valid_course_category();
	abstract function convert_to_new_course_category();
	abstract static function get_all($parameters);
}
?>