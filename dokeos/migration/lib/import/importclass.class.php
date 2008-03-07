<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a class
 * @author Sven Vanpoucke
 */
abstract class ImportClass extends Import
{
	abstract function is_valid_class();
	abstract function convert_to_new_class();
	abstract static function get_all_classes();
}
?>
