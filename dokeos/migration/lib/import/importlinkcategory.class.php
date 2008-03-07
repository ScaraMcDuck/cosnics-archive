<?php

/**
 * @package migration.lib.import
 */

abstract class ImportLinkCategory extends Import
{
	abstract function convert_to_new_link_category();
	abstract static function get_all_link_categories();
}
?>