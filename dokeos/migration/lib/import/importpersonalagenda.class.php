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
	abstract function is_valid_personal_agenda();
	abstract function convert_to_new_personal_agenda();
	abstract static function get_all_personal_agendas($mgdm);
}
?>
