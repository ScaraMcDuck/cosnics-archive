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
	abstract function is_valid_document($course);
	abstract function convert_to_new_document($course);
	abstract static function get_all_documents($course, $mgdm, $include_deleted_files);
}
?>
