<?php
/**
 * @package contentboxes.install
 */
require_once dirname(__FILE__).'/../lib/trackingdatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
/**
 * This	 installer can be used to create the contentboxes structure
 */
class TrackingInstaller extends Installer
{
	/**
	 * Constructor
	 */
	function TrackingInstaller()
	{
	}
	
	/**
	 * Runs the install-script. Creates the necessary tables for contentbox storage
	 */
	function install()
	{
		$dir	= dirname(__FILE__);
		$files	= FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'xml'))
			{
				if (!$this->create_storage_unit($file))
				{
					return array('success' => false, 'message' => $this->retrieve_message());
				}
			}
		}
		
		$success_message = '<span style="color: green; font-weight: bold;">' . Translation :: get('ApplicationInstallSuccess') . '</span>';
		$this->add_message($success_message);
		return array('success' => true, 'message' => $this->retrieve_message());
	}
	
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		$dm = TrackingDataManager :: get_instance();
		$this->add_message(Translation :: get('StorageUnitCreation') . ': <em>'.$storage_unit_info['name'] . '</em>');
		if (!$dm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']))
		{
			$error_message = '<span style="color: red; font-weight: bold;">' . Translation :: get('StorageUnitCreationFailed') . ': <em>'.$storage_unit_info['name'] . '</em></span>';
			$this->add_message($error_message);
			
			return false;
		}
		else
		{
			return true;
		}

	}
}
?>