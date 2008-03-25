<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a group user relation
 * @author Sven Vanpoucke
 */
abstract class ImportGroupRelUser extends Import
{
	abstract function is_valid_group_rel_user($course);
	abstract function convert_to_new_group_rel_user($course);
	abstract static function get_all($parameters);
}
?>