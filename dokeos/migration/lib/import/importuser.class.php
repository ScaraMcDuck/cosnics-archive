<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a user
 * @author Van Wayenbergh David
 */

abstract class ImportUser extends Import
{
	abstract function is_valid_user($lcms_users);
	abstract function convert_to_new_user();
	abstract static function get_all_users();
}
?>
