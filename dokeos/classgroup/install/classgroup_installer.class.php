<?php
/**
 * @package classgroup.install
 */
require_once dirname(__FILE__).'/../lib/classgroupdatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
/**
 * This installer can be used to create the storage structure for the
 * classgroup application.
 */
class ClassgroupInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function ClassgroupInstaller($values)
    {
    	parent :: __construct($values, ClassgroupDataManager :: get_instance());
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
	
	/**
	 * Registers the trackers, events and creates the storage units for the trackers
	 */
}
?>