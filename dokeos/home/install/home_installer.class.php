<?php
/**
 * @package application.home
 */
require_once dirname(__FILE__).'/../lib/homedatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * home application.
 */
class HomeInstaller extends Installer
{
	private $hdm;
	/**
	 * Constructor
	 */
    function HomeInstaller()
    {
    	$this->hdm = HomeDataManager :: get_instance();
    }
	/**
	 * Runs the install-script.
	 * @todo This function now uses the function of the RepositoryInstaller
	 * class. These shared functions should be available in a common base class.
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
		
		if (!$this->create_basic_home())
		{
			return array('success' => false, 'message' => $this->retrieve_message());
		}
		else
		{
			$this->add_message(Translation :: get('HomeCreated'));
		}
		
		$success_message = '<span style="color: green; font-weight: bold;">' . Translation :: get('ApplicationInstallSuccess') . '</span>';
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
		$this->add_message(Translation :: get('StorageUnitCreation') . ': <em>'.$storage_unit_info['name'] . '</em>');
		if (!$this->hdm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']))
		{
			$error_message = '<span style="color: red; font-weight: bold;">' . Translation :: get('StorageUnitCreationFailed') . ': <em>'.$storage_unit_info['name'] . '</em></span>';
			$this->add_message($error_message);
			$this->add_message(Translation :: get('ApplicationInstallFailed'));
			$this->add_message(Translation :: get('PlatformInstallFailed'));
			
			return false;
		}
		else
		{
			return true;
		}

	}
	
	function create_basic_home()
	{
		$row = new HomeRow();
		$row->set_title(Translation :: get('Site'));
		if (!$row->create())
		{
			return false;
		}
		
		$column_news = new HomeColumn();
		$column_news->set_row($row->get_id());
		$column_news->set_title(Translation :: get('News'));
		$column_news->set_sort('1');
		$column_news->set_width('505');
		if (!$column_news->create())
		{
			return false;
		}
		
		$block_user = new HomeBlock();
		$block_user->set_column($column_news->get_id());
		$block_user->set_title(Translation :: get('PersonalCalendar'));
		$block_user->set_component('PersonalCalendar.Month');
		if (!$block_user->create())
		{
			return false;
		}
		
		$column_varia = new HomeColumn();
		$column_varia->set_row($row->get_id());
		$column_varia->set_title(Translation :: get('Various'));
		$column_varia->set_sort('2');
		$column_varia->set_width('350');
		if (!$column_varia->create())
		{
			return false;
		}
		
		$block_user = new HomeBlock();
		$block_user->set_column($column_varia->get_id());
		$block_user->set_title(Translation :: get('User'));
		$block_user->set_component('User');
		if (!$block_user->create())
		{
			return false;
		}
		
		return true;
	}
}
?>