<?php
/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a course class
 * @author Sven Vanpoucke
 */
abstract class ImportCourseRelClass extends Import
{
	abstract function is_valid_course_rel_class();
	abstract function convert_to_new_course_rel_class();
	abstract static function get_all($parameters);
}
?>
