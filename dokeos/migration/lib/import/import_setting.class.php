<?php

/**
 * @package migration
 */

abstract class Import_Setting extends Import
{
	abstract function convertToNewSetting();
	abstract static function GetAllSettings();
}
?>
