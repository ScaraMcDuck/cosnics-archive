<?php
/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a tool
 * @author Van Wayenbergh David
 */
abstract class ImportTool extends Import
{
	abstract function is_valid_tool();
	abstract function convert_to_new_tool($db);
	abstract static function get_all_tools($mgdm,$db);
}
?>