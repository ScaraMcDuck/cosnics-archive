<?php

/**
 * @package migration.lib.import
 */

abstract class ImportGroup extends Import
{
	abstract function convert_to_new_group();
	abstract static function get_all_groups();
}
?>
