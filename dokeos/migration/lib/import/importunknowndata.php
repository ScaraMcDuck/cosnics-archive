<?php

/**
 * @package migration.lib.import
 */

abstract class ImportUnknownData
{
	abstract function convert_to_learning_object();
	abstract static function get_all_unknown_data();
}

?>
