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
	abstract function is_valid($parameters);
	abstract function convert_to_lcms($parameters);
	abstract static function get_all($parameters);
}
?>
