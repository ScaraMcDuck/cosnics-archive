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
		
		// Get the learning object xml-files if they exist
		$files	= FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'xml'))
			{
				// Create the learning object table that stores the additional lo-properties
				if (!$this->create_storage_unit($file))
				{
					return false;
				}
			}
		}
		
		// Register the learning objects
		$folders	= FileSystem :: get_directory_content($dir, FileSystem :: LIST_DIRECTORIES, false);
		
		foreach($folders as $folder)
		{
			if($folder == '.svn') continue;
			
			$this->add_message(self :: TYPE_NORMAL, Translation :: get('LearningObjectRegistration') . ': <em>'.Translation :: get(LearningObject :: type_to_class($folder) . 'TypeName') . '</em>');
			
			$learning_object_registration = new Registration();
			$learning_object_registration->set_type(Registration :: TYPE_LEARNING_OBJECT);
			$learning_object_registration->set_name($folder);
			$learning_object_registration->set_status(Registration :: STATUS_ACTIVE);
			
			if (!$learning_object_registration->create())
			{
				return $this->installation_failed(Translation :: get('LearningObjectRegistrationFailed'));
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