<?php

/**
 * @package migration.lib.import
 */

abstract class Import_Link extends Import
{
	abstract function convertToNewLink();
	abstract static function GetAllLinks();
}
?>
