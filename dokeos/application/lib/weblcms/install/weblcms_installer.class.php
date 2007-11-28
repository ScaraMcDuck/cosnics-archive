<?php
/**
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../../../common/installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * weblcms application.
 */
class WeblcmsInstaller extends Installer {

	private $dm;
	/**
	 * Constructor
	 */
    function WeblcmsInstaller() {
    	$this->dm = WeblcmsDataManager :: get_instance();
    }
	/**
	 * Runs the install-script.
	 */
	function install()
	{
		$sqlfiles = array();
		//Todo: Use FileSystem::get_directory_content to get xml files
		$dir = dirname(__FILE__);
		$handle = opendir($dir);
		while (false !== ($type = readdir($handle)))
		{
			$path = $dir.'/'.$type;
			if (file_exists($path) && (substr($path, -3) == 'xml'))
			{
				$this->create_storage_unit($path);
			}
		}
		closedir($handle);
	}
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		echo '<pre>Creating WebLcms Storage Unit: '.$storage_unit_info['name'].'</pre>';flush();
		$this->dm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']);

	}
}
?>