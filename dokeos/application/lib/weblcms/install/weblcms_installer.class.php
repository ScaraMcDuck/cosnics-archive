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
		echo '<div class="learning_object" style="padding: 15px 15px 15px 76px; background-image: url(../img/admin_weblcms.gif);">';
		echo '<div class="title">'. get_lang('AppWeblcms') .'</div>';
		echo '<div class="description">';
		
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
		
		echo '<br /><span style="color: #008000; font-weight: bold;">'. get_lang('ApplicationSuccess') .'</span>';
		echo '</div>';
		echo '</div>';
	}
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		echo 'Creating WebLcms Storage Unit: '.$storage_unit_info['name'].'<br />';flush();
		$this->dm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']);

	}
}
?>