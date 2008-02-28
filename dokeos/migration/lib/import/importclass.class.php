<?php

/**
 * @package migration.lib.import
 */

abstract class ImportClass extends Import
{
	abstract function convert_to_new_class();
	abstract static function get_all_classes();
}
?>
