<?php

/**
 * @package migration.lib.import
 */

abstract class Import_Setting extends Import
{
	abstract function convertToNewSetting();
	abstract static function GetAllSettings();
}
?>
