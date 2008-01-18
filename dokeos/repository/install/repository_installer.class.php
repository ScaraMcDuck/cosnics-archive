<?php
/**
 * $Id: repositorydatamanager.class.php 9176 2006-08-30 09:08:17Z bmol $
 * @package repository
 */
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../common/installer.class.php';
/**
 * This	 installer can be used to create the storage structure for the
 * repository.
 */
class RepositoryInstaller extends Installer
{
	/**
	 * Constructor
	 */
	function RepositoryInstaller()
	{
	}
	/**
	 * Runs the install-script. After creating the necessary tables to store the
	 * common learning object information, this function will scan the
	 * directories of all learning object types. When an XML-file describing a
	 * storage unit is found, this function will parse the file and create the
	 * storage unit.
	 */
	function install()
	{
		echo '<div class="learning_object" style="padding: 15px 15px 15px 76px; background-image: url(../img/admin_repository.gif);">';
		echo '<div class="title">'. get_lang('Repository') .'</div>';
		echo '<div class="description">';
		$dir = dirname(__FILE__).'/../lib/learning_object';
		$handle = opendir($dir);
		while (false !== ($type = readdir($handle)))
		{
			$path = $dir.'/'.$type.'/'.$type.'.xml';
			if (file_exists($path))
			{
				$this->create_storage_unit($path);
			}
		}
		closedir($handle);

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

	/**
	 * Parses an XML-file in which a storage unit is described. After parsing,
	 * the create_storage_unit function of the RepositoryDataManager is used to
	 * create the actual storage unit depending on the implementation of the
	 * datamanager.
	 * @param string $path The path to the XML-file to parse
	 */
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		$dm = RepositoryDataManager :: get_instance();
		echo 'Creating Repository Storage Unit: '.$storage_unit_info['name'].'<br />';flush();
		$dm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']);

	}
}
?>