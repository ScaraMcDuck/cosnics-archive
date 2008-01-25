<?php
/**
 * @package application.lib.profiler.install
 */
require_once dirname(__FILE__).'/../profilerdatamanager.class.php';
require_once dirname(__FILE__).'/../../../../common/installer.class.php';
require_once dirname(__FILE__).'/../../../../common/filesystem/filesystem.class.php';
/**
 * This installer can be used to create the storage structure for the
 * profiler application.
 */
class ProfilerInstaller extends Installer {

	private $pdm;
	/**
	 * Constructor
	 */
    function ProfilerInstaller()
    {
    	$this->pdm = ProfilerDataManager :: get_instance();
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
		
		$success_message = '<span style="color: green; font-weight: bold;">' . get_lang('ApplicationInstallSuccess') . '</span>';
		$this->add_message($success_message);
		return array('success' => true, 'message' => $this->retrieve_message());
	}
	
	/**
	 * Parses an XML file and sends the request to the database manager
	 * @param String $path
	 */
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		$this->add_message(get_lang('StorageUnitCreation') . ': <em>'.$storage_unit_info['name'] . '</em>');
		if (!$this->pdm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']))
		{
			$error_message = '<span style="color: red; font-weight: bold;">' . get_lang('StorageUnitCreationFailed') . ': <em>'.$storage_unit_info['name'] . '</em></span>';
			$this->add_message($error_message);
			$this->add_message(get_lang('ApplicationInstallFailed'));
			$this->add_message(get_lang('PlatformInstallFailed'));
			
			return false;
		}
		else
		{
			return true;
		}
	}
}
?>