<?php

/**
 * @package migration.lib.import
 */

abstract class Import_Document extends Import
{
	abstract function convertToNewDocument();
	abstract static function GetAllDocuments();
}
?>
