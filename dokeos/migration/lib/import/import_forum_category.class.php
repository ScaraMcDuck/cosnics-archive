<?php
/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a forum category
 * @author Van Wayenbergh David
 */
abstract class ImportForumCategory extends Import
{
	abstract function is_valid($array);
	abstract function convert_to_lcms($array);
	abstract static function get_all($array);
}
?>