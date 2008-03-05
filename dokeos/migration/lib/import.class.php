<?php

/**
 * @package migration
 */

abstract class Import
{
	static function factory($old_system, $type)
	{
		$filename = dirname(__FILE__) . '/../platform/'.strtolower($old_system) . '/' . 
			strtolower($old_system) . strtolower($type) . '.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			echo($filename);
			die('Failed to load ' . $old_system . $type . '.class.php');
		}
		$class = $old_system . $type;
		require_once $filename;
		return new $class();
	}	
	
	function precheck()
	{
		
	}	
	
	function postcheck()
	{
		
	}
}

?>