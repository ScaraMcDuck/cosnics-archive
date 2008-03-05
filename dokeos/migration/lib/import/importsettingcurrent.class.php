<?php

/**
 * @package migration.lib.import
 */

abstract class ImportSettingCurrent extends Import
{
	abstract function convert_to_new_admin_setting();
	abstract static function get_all_current_settings();
}
?>
