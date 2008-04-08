<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a current setting
 * @author Van Wayenbergh David
 */

abstract class ImportSettingCurrent extends Import
{
	abstract function is_valid_current_setting();
	abstract function convert_to_new_admin_setting();
	abstract static function get_all_current_settings($mgdm);
}
?>
