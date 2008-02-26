<?php

/**
 * @package migration
 */

abstract class Import_Document extends Import
{
	abstract function convertToNewDocument();
	abstract static function GetAllDocuments();
}
?>
