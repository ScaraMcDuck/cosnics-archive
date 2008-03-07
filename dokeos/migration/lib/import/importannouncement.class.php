<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines an announcement
 * @author Sven Vanpoucke
 */
abstract class ImportAnnouncement extends Import
{
	abstract function is_valid_announcement();
	abstract function convert_to_new_announcement();
	abstract static function get_all_announcements($mgdm);
}

?>
