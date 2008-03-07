<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a link
 * @author Sven Vanpoucke
 */
abstract class ImportLink extends Import
{
	abstract function is_valid_link();
	abstract function convert_to_new_link();
	abstract static function get_all_links();
}
?>
