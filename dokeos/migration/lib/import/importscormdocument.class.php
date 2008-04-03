<?php
/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a scorm document import
 * @author Sven Vanpoucke
 */
abstract class ImportScormDocument extends Import
{
	abstract function is_valid($array);
	abstract function convert_to_lcms($array);
	abstract static function get_all($array);
}
?>