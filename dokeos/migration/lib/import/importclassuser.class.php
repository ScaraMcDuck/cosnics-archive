<?php
/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a class user
 * @author Sven Vanpoucke
 */
abstract class ImportClassUser extends Import
{
	abstract function is_valid_class_user();
	abstract function convert_to_new_class_user();
	abstract static function get_all($array);
}
?>