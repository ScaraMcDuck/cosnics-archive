<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a personal agenda
 * @author Van Wayenbergh David
 */

abstract class ImportPersonalAgenda extends Import
{
	abstract function is_valid();
	abstract function convert_to_lcms();
	abstract static function get_all($array);
}
?>
