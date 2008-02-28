<?php

abstract class MigrationDataManager
{
	abstract function validate_settings($parameters);
	abstract function move_file($old_rel_path, $new_rel_path,$filename);
	abstract function create_directory($is_new_system, $rel_path);
	
	private static $instances = array();
	
	/**
	 * Singleton and factory pattern in one
	 */
	static function getInstance($platform)
	{
		if(!isset(self :: $instances[$platform]))
		{
			$filename = dirname(__FILE__) . '/../platform/' . strtolower($platform) . '/' . 
				strtolower($platform) . 'datamanager.class.php';
			if (!file_exists($filename) || !is_file($filename))
			{
				echo($filename);
				die('Failed to load ' . $platform . 'datamanager.class.php');
			}
			$class = $platform . 'DataManager';
			require_once $filename;
			self :: $instances[$platform] = new $class();
		}
		
		return self :: $instances[$platform];
	}
}

?>
