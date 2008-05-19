<?php
/**
 * @package contentboxes.install
 */
require_once dirname(__FILE__).'/../lib/trackingdatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_tracking_path() .'lib/trackerregistration.class.php';
require_once Path :: get_tracking_path() .'lib/eventreltracker.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
/**
 * This	 installer can be used to create the contentboxes structure
 */
class TrackingInstaller extends Installer
{
	/**
	 * Constructor
	 */
	function TrackingInstaller($values)
	{
		parent :: __construct($values, TrackingDataManager :: get_instance());
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
					return false;
				}
			}
		}
		
		return $this->installation_successful();
	}
}
?>