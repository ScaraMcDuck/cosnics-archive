<?php
/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a blog task rel user
 * @author Sven Vanpoucke
 */
abstract class ImportBlogTaskRelUser extends Import
{
	abstract function is_valid($array);
	abstract function convert_to_lcms($array);
	abstract static function get_all($array);
}
?>