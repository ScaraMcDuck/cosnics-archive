<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a link category
 * @author Van Wayenbergh David
 */

abstract class ImportLinkCategory extends Import
{
	abstract function is_valid_link_category($parameters);
	abstract function convert_to_new_link_category($parameters);
	abstract static function get_all($parameters);
}
?>