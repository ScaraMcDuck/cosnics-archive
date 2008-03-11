<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a group category
 * @author Sven Vanpoucke
 */
abstract class ImportGroupCategory extends Import
{
	abstract function is_valid_group_category($course);
	abstract function convert_to_new_group_category($course);
	abstract static function get_all_group_categories($course, $mgdm);
}
?>