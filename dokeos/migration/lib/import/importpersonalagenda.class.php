<?php

/**
 * @package migration.lib.import
 */

abstract class ImportPersonalAgenda extends Import
{
	abstract function convert_to_new_personal_agenda();
	abstract static function get_all_personal_agendas($mgdm);
}
?>
