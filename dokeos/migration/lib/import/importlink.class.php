<?php

/**
 * @package migration.lib.import
 */

abstract class ImportLink extends Import
{
	abstract function convert_to_new_link();
	abstract static function get_all_links();
}
?>
