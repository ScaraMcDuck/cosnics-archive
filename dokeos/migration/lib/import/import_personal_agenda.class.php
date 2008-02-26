<?php

/**
 * @package migration
 */

abstract class Import_Personal_Agenda extends Import
{
	abstract function convertToNewPersonal_Agenda();
	abstract static function GetAllPersonal_Agendas();
}
?>
