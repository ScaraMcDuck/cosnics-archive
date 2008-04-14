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
	abstract function is_valid($parameters);
	abstract function convert_to_lcms($parameters);
	abstract static function get_all($parameters);
}
?>
