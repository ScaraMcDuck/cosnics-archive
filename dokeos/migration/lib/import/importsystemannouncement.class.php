<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a system announcement
 * @author Van Wayenbergh David
 */

abstract class ImportSystemAnnouncement extends Import
{
	abstract function is_valid_system_announcement();
	abstract function convert_to_new_system_announcement($admin);
	abstract static function get_all($parameters);
}

?>
