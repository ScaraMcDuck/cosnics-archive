<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a resource
 * @author Sven Vanpoucke
 */
abstract class ImportResource extends Import
{
	abstract function is_valid_resource();
	abstract function convert_to_new_resource();
	abstract static function get_all_resources($mgdm);
}

?>