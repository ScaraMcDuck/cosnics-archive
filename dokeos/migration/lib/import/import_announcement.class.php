<?php

/**
 * @package migration.lib.import
 */

abstract class Import_Announcement extends Import
{
	abstract function convertToNewAnnouncement();
	abstract static function GetAllAnnouncements();
}

?>
