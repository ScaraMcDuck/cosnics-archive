<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a document
 * @author Sven Vanpoucke
 */
abstract class ImportDocument extends Import
{
	abstract function is_valid_document();
	abstract function convert_to_new_document();
	abstract static function get_all_documents();
}
?>
