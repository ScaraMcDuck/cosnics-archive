<?php
/**
 * @package application.home
 */
require_once dirname(__FILE__).'/../lib/home_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * home application.
 */
class HomeInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function HomeInstaller($values)
    {
    	parent :: __construct($values, HomeDataManager :: get_instance());
    }
	/**
	 * Runs the install-script.
	 * @todo This function now uses the function of the RepositoryInstaller
	 * class. These shared functions should be available in a common base class.
	 */
	function install_extra()
	{		
		if (!$this->create_basic_home())
		{
			return false;
		}
		else
		{
			$this->add_message(self :: TYPE_NORMAL, Translation :: get('HomeCreated'));
		}
		
		return true;
	}
	
	function create_basic_home()
	{
		$row = new HomeRow();
		$row->set_title(Translation :: get('Site'));
		$row->set_user('1');
		if (!$row->create())
		{
			return false;
		}
		
		$column_news = new HomeColumn();
		$column_news->set_row($row->get_id());
		$column_news->set_title(Translation :: get('News'));
		$column_news->set_sort('1');
		$column_news->set_width('50');
		$column_news->set_user('0');
		if (!$column_news->create())
		{
			return false;
		}
		
		$block_test = new HomeBlock();
		$block_test->set_column($column_news->get_id());
		$block_test->set_title('Extra');
		$block_test->set_application('personal_calendar');
		$block_test->set_component('extra');
		$block_test->set_user('0');
		if (!$block_test->create())
		{
			return false;
		}
		
		$column_varia = new HomeColumn();
		$column_varia->set_row($row->get_id());
		$column_varia->set_title(Translation :: get('Various'));
		$column_varia->set_sort('2');
		$column_varia->set_width('23');
		$column_varia->set_user('0');
		if (!$column_varia->create())
		{
			return false;
		}
		
		$block_user = new HomeBlock();
		$block_user->set_column($column_varia->get_id());
		$block_user->set_title(Translation :: get('User'));
		$block_user->set_application('user');
		$block_user->set_component('login');
		$block_user->set_user('0');
		if (!$block_user->create())
		{
			return false;
		}
		
		$block_weblcms = new HomeBlock();
		$block_weblcms->set_column($column_varia->get_id());
		$block_weblcms->set_title(Translation :: get('Weblcms'));
		$block_weblcms->set_application('weblcms');
		$block_weblcms->set_component('extra');
		$block_weblcms->set_user('0');
		if (!$block_weblcms->create())
		{
			return false;
		}
		
		$block_search = new HomeBlock();
		$block_search->set_column($column_varia->get_id());
		$block_search->set_title(Translation :: get('Search'));
		$block_search->set_application('search_portal');
		$block_search->set_component('extra');
		$block_search->set_user('0');
		if (!$block_search->create())
		{
			return false;
		}
		
		$column_extra = new HomeColumn();
		$column_extra->set_row($row->get_id());
		$column_extra->set_title(Translation :: get('Extra'));
		$column_extra->set_sort('3');
		$column_extra->set_width('25');
		$column_extra->set_user('0');
		if (!$column_extra->create())
		{
			return false;
		}
		
		$block_user = new HomeBlock();
		$block_user->set_column($column_extra->get_id());
		$block_user->set_title(Translation :: get('PersonalCalendar'));
		$block_user->set_application('personal_calendar');
		$block_user->set_component('month');
		$block_user->set_user('0');
		if (!$block_user->create())
		{
			return false;
		}	
		
		return true;
	}
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>