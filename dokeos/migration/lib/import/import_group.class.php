<?php

/**
 * @package migration
 */

abstract class Import_Group extends Import
{
	abstract function convertToNewGroup();
	abstract static function GetAllGroups();
}
?>
