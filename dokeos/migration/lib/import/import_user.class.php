<?php

/**
 * @package migration
 */

abstract class Import_User extends Import
{
	abstract function convertToNewUser();
	abstract static function GetAllUsers();
}
?>
