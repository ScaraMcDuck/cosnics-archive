<?php
/**
 * @package application.lib.profiler.install
 */
require_once dirname(__FILE__).'/../profilerdatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';
/**
 * This installer can be used to create the storage structure for the
 * profiler application.
 */
class ProfilerInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function ProfilerInstaller($values)
    {
    	parent :: __construct($values, ProfilerDataManager :: get_instance());
    }
	/**
	 * Runs the install-script.
	 */
	function install()
	{
		$dir = dirname(__FILE__);
		$files = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
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
		
		if(!$this->register_trackers())
		{
			return array('success' => false, 'message' => $this->retrieve_message());
		}
		
		$success_message = '<span style="color: green; font-weight: bold;">' . Translation :: get('ApplicationInstallSuccess') . '</span>';
		$this->add_message($success_message);
		return array('success' => true, 'message' => $this->retrieve_message());
	}
}
?>