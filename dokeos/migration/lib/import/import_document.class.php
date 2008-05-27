<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a document
 * @author Sven Vanpoucke
 */
abstract class ImportDocument extends Import
{
	abstract function is_valid($array);
	abstract function convert_to_lcms($array);
	abstract static function get_all($array);
}
?>
