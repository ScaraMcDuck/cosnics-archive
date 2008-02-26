<?php

/**
 * @package migration
 */

abstract class Import_Announcement extends Import
{
	abstract function convertToNewAnnouncement();
	abstract static function GetAllAnnouncements();
}

?>
