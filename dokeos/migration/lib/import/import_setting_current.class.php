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
	abstract function is_valid($parameters);
	abstract function convert_to_lcms($parameters);
	abstract static function get_all($parameters);
}
?>
