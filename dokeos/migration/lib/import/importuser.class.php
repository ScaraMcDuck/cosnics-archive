<?php

/**
 * @package migration.lib.import
 */

abstract class ImportUser extends Import
{
	abstract function convert_to_new_user();
	abstract static function get_all_users();
}
?>
