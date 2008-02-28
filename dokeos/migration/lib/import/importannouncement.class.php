<?php

/**
 * @package migration.lib.import
 */

abstract class ImportAnnouncement extends Import
{
	abstract function convert_to_new_announcement();
	abstract static function get_all_announcements();
}

?>
