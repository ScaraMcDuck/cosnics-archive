<?php

abstract class MigrationDataManager
{
	abstract function validateSettings($parameters);
	
	private static $instances = array();
	
	/**
	 * Singleton and factory pattern in one
	 */
	static function getInstance($platform)
	{
		if(!isset(self :: $instances[$platform]))
		{
			$filename = dirname(__FILE__) . '/../platform/' . strtolower($platform) . '/' . 
				strtolower($platform) . '_datamanager.class.php';
			if (!file_exists($filename) || !is_file($filename))
			{
				echo($filename);
				die('Failed to load ' . $platform . '_datamanager.class.php');
			}
			$class = $platform . '_DataManager';
			require_once $filename;
			self :: $instances[$platform] = new $class();
		}
		
		return self :: $instances[$platform];
	}
}

?>
