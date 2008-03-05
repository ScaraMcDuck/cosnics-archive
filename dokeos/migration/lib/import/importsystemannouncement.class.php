<?php

/**
 * @package migration.lib.import
 */

abstract class ImportSystemAnnouncement extends Import
{
	abstract function convert_to_new_system_announcement($admin);
	abstract static function get_all_system_announcements();
}

?>
