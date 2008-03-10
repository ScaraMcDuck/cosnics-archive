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
	abstract function is_valid_group($course);
	abstract function convert_to_new_group($course);
	abstract static function get_all_groups($course, $mgdm);
}
?>
