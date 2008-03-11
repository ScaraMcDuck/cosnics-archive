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
	abstract function is_valid_link($course);
	abstract function convert_to_new_link($course);
	abstract static function get_all_links($db, $include_deleted_files);
}
?>
