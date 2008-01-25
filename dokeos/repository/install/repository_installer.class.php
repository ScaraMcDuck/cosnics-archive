<?php
/**
 * $Id: repositorydatamanager.class.php 9176 2006-08-30 09:08:17Z bmol $
 * @package repository
 */
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../common/installer.class.php';
require_once dirname(__FILE__).'/../../common/filesystem/filesystem.class.php';
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
		$dir_lo		= dirname(__FILE__) . '/../lib/learning_object';
		$dir_app	= dirname(__FILE__);
		$files_lo	= FileSystem :: get_directory_content($dir_lo, FileSystem :: LIST_FILES);
		$files_app	= FileSystem :: get_directory_content($dir_app, FileSystem :: LIST_FILES);
		
		$files = array_merge($files_app, $files_lo);
		
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
		$this->add_message(get_lang('StorageUnitCreation') . ': <em>'.$storage_unit_info['name'] . '</em>');
		if (!$dm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']))
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