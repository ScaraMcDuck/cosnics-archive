<?php

abstract class Import_Announcement 
{
	abstract function convertToNewAnnouncement();
	abstract static function GetAllAnnouncements();
	
	static function factory($type, $migration_manager)
	{
		$filename = dirname(__FILE__) . '/../'.strtolower($type) . '/' . strtolower($type) 
			. '_announcements.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load ' . $type . '_announcements.class.php');
		}
		$class = $type.'_Announcements';
		require_once $filename;
		return new $class($migration_manager);
	}

}

?>
