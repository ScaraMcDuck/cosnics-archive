<?php

/**
 * @package migration.lib.import
 */

abstract class Import_Class extends Import
{
	abstract function convertToNewClass();
	abstract static function GetAllClasses();
}
?>
