<?php
/**
 * @package application.menu
 */
require_once dirname(__FILE__).'/../lib/menudatamanager.class.php';
require_once dirname(__FILE__).'/../lib/menuitem.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * menu application.
 */
class MenuInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function MenuInstaller($values)
    {
    	parent :: __construct($values, MenuDataManager :: get_instance());
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
					return false;
				}
			}
		}
		
		if (!$this->create_basic_menu())
		{
			return false;
		}
		else
		{
			$this->add_message(self :: TYPE_NORMAL, Translation :: get('MenuCreated'));
		}
		
		if(!$this->register_trackers())
		{
			return false;
		}
		
		return $this->installation_successful();
	}
	
	function create_basic_menu()
	{
		// TODO: Replace static menu items with some kind of dynamic pregenerated menu 
		$menu_item = new MenuItem();
		$menu_item->set_title('WebLcms');
		$menu_item->set_application('weblcms');
		$menu_item->set_section('weblcms');
		$menu_item->create();
		
		$personal_item = new MenuItem();
		$personal_item->set_title('Personal');
		$personal_item->set_section('personal');
		$personal_item->create();
		
		$menu_item = new MenuItem();
		$menu_item->set_title('Personal Calendar');
		$menu_item->set_application('personal_calendar');
		$menu_item->set_section('personal_calendar');
		$menu_item->set_category($personal_item->get_id());
		$menu_item->create();
		
		$menu_item = new MenuItem();
		$menu_item->set_title('Personal Messenger');
		$menu_item->set_application('personal_messenger');
		$menu_item->set_section('personal_messenger');
		$menu_item->set_category($personal_item->get_id());
		$menu_item->create();		
		
		$menu_item = new MenuItem();
		$menu_item->set_title('My Portfolio');
		$menu_item->set_application('myportfolio');
		$menu_item->set_section('myportfolio');
		$menu_item->set_category($personal_item->get_id());
		$menu_item->create();
		
		$menu_item = new MenuItem();
		$menu_item->set_title('Profiler');
		$menu_item->set_application('profiler');
		$menu_item->set_section('profiler');
		$menu_item->create();
		
		$menu_item = new MenuItem();
		$menu_item->set_title('Search Portal');
		$menu_item->set_application('search_portal');
		$menu_item->set_section('search_portal');
		$menu_item->create();		
		
		return true;
	}
}
?>