<?php
/**
 * $Id: personal_calendar_installer.class.php 12686 2007-07-03 11:32:57Z bmol $
 * @package users.install
 */
require_once dirname(__FILE__).'/../lib/usersdatamanager.class.php';
require_once dirname(__FILE__).'/../../common/installer.class.php';
/**
 * This installer can be used to create the storage structure for the
 * users application.
 */
class UsersInstaller extends Installer{
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
		echo '<div class="learning_object" style="padding: 15px 15px 15px 76px; background-image: url(../img/admin_users.gif);">';
		echo '<div class="title">'. get_lang('Users') .'</div>';
		echo '<div class="description">';
		$sqlfiles = array();
		$dir = dirname(__FILE__);
		//Todo: Use FileSystem::get_directory_content to get xml files
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
		echo '<br /><span style="color: #008000; font-weight: bold;">'. get_lang('ApplicationSuccess') .'</span>';
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Parses an XML file and sends the request to the database manager
	 * @param String $path
	 */
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		$dm = UsersDataManager :: get_instance();
		echo 'Creating User Storage Unit: '.$storage_unit_info['name'].'<br />';flush();
		$dm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']);

	}
}
?>