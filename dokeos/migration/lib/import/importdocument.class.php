<?php

/**
 * @package migration.lib.import
 */

abstract class ImportDocument extends Import
{
	abstract function convert_to_new_document();
	abstract static function get_all_documents();
}
?>
