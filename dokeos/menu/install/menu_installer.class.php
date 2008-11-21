<?php
/**
 * @package application.menu
 */
require_once dirname(__FILE__).'/../lib/menu_data_manager.class.php';
require_once dirname(__FILE__).'/../lib/menu_item.class.php';
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
	function install_extra()
	{
		if (!$this->create_basic_menu())
		{
			return false;
		}
		else
		{
			$this->add_message(self :: TYPE_NORMAL, Translation :: get('MenuCreated'));
		}
		
		return true;
	}
	
	function create_basic_menu()
	{
		// TODO: Replace static menu items with some kind of dynamic pregenerated menu 
		$menu_item = new MenuItem();
		$menu_item->set_title('WebLcms');
		$menu_item->set_application('weblcms');
		$menu_item->set_section('weblcms');
		$menu_item->set_category(0);
		$menu_item->create();
		
		$personal_item = new MenuItem();
		$personal_item->set_title('Personal');
		$personal_item->set_section('personal');
		$personal_item->set_category(0);
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
		$menu_item->set_category(0);
		$menu_item->create();
		
		$menu_item = new MenuItem();
		$menu_item->set_title('Search Portal');
		$menu_item->set_application('search_portal');
		$menu_item->set_section('search_portal');
		$menu_item->set_category(0);
		$menu_item->create();		
		
		return true;
	}
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>