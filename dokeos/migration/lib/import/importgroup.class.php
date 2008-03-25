<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a group
 * @author Sven Vanpoucke
 */
abstract class ImportGroup extends Import
{
	abstract function is_valid_group($array);
	abstract function convert_to_new_group($array);
	abstract static function get_all($array);
}
?>
