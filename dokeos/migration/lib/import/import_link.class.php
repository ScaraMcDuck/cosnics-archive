<?php

/**
 * @package migration
 */

abstract class Import_Link extends Import
{
	abstract function convertToNewLink();
	abstract static function GetAllLinks();
}
?>
