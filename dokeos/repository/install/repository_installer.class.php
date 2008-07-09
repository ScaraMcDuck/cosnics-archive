<?php
/**
 * $Id: repository_data_manager.class.php 9176 2006-08-30 09:08:17Z bmol $
 * @package repository
 */
require_once dirname(__FILE__).'/../lib/repository_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';
/**
 * This	 installer can be used to create the storage structure for the
 * repository.
 */
class RepositoryInstaller extends Installer
{
	/**
	 * Constructor
	 */
	function RepositoryInstaller($values)
	{
		parent :: __construct($values, RepositoryDataManager :: get_instance());
	}
	/**
	 * Runs the install-script. After creating the necessary tables to store the
	 * common learning object information, this function will scan the
	 * directories of all learning object types. When an XML-file describing a
	 * storage unit is found, this function will parse the file and create the
	 * storage unit.
	 */
	function install_extra()
	{
		$rdm	= $this->get_data_manager();
		$dir	= dirname(__FILE__) . '/../lib/learning_object';
		$files	= FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'xml'))
			{
				// Create the lo-table that stores the additional lo-properties
				if (!$this->create_storage_unit($file))
				{
					return false;
				}
			}
		}
		
		return true;
	}
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>