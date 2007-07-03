<?php
/**
 * @package users.install
 */
require_once dirname(__FILE__).'/../lib/usersdatamanager.class.php';
require_once dirname(__FILE__).'/../../../common/installer.class.php';
/**
 * This installer can be used to create the storage structure for the
 * users application.
 */
class UsersInstaller {
	/**
	 * Constructor
	 */
    function UsersInstaller() {
    }
	/**
	 * Runs the install-script.
	 */
	function install()
	{
		$sqlfiles = array();
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

	/**
	 * Parses an XML file and sends the request to the database manager
	 * @param String $path
	 */
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		$dm = UsersDataManager :: get_instance();
		echo '<pre>Creating PersonalCalendar Storage Unit: '.$storage_unit_info['name'].'</pre>';flush();
		$dm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']);

	}
}
?>