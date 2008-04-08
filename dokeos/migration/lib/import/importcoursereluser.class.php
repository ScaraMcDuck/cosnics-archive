<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a course user
 * @author Sven Vanpoucke
 */
abstract class ImportCourseRelUser extends Import
{
	abstract function is_valid_course_user_relation();
	abstract function convert_to_new_course_user_relation();
	abstract static function get_all($parameters);
}
?>