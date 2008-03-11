<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a course
 * @author Sven Vanpoucke
 */
abstract class ImportCourseDescription extends Import
{
	abstract function is_valid_course_description();
	abstract function convert_to_new_course_description($course);
	abstract static function get_all_course_descriptions($mgdm, $db);
}
?>
