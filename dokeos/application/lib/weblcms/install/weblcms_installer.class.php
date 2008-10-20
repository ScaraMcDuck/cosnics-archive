<?php
/**
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../weblcms_manager/weblcms.class.php';
require_once dirname(__FILE__).'/../weblcms_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * weblcms application.
 */
class WeblcmsInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function WeblcmsInstaller($values)
    {
    	parent :: __construct($values, WeblcmsDataManager :: get_instance());
    }
	/**
	 * Runs the install-script.
	 */
	function install_extra()
	{
//		if (!$this->create_weblcms_root_location())
//		{
//			$this->add_message(self :: TYPE_ERROR, Translation :: get('RightsLocationNotAdded'));
//			return false;
//		}
//		else
//		{
//			$this->add_message(self :: TYPE_NORMAL, Translation :: get('RightsLocationAdded'));
//		}
//		
//		if (!$this->create_default_categories_in_weblcms($root_location))
//		{
//			return false;
//		}
//		else
//		{
//			$this->add_message(self :: TYPE_NORMAL, Translation :: get('DefaultWeblcmsCategoriesCreated'));
//		}
		
		return true;
	}
	
	function create_default_categories_in_weblcms($root_location)
	{
		//Creating Language Skills
		$cat = new CourseCategory();
		$cat->set_name('Language skills');
		$cat->set_parent('0');
		$cat->set_display_order(1);
		if (!$cat->create())
		{
			return false;
		}
		
		$location = new Location();
		$location->set_location($cat->get_name());
		$location->set_application(Weblcms :: APPLICATION_NAME);
		$location->set_type('category');
		$location->set_identifier($cat->get_id());
		$location->set_left_value('1');
		$location->set_right_value('2');
		$location->set_parent($root_location->get_id());
		
		if (!$location->create())
		{
			return false;
		}
	
		//creating PC Skills
		$cat = new CourseCategory();
		$cat->set_name('PC skills');
		$cat->set_parent('0');
		$cat->set_display_order(1);
		if (!$cat->create())
		{
			return false;
		}
		
		$location = new Location();
		$location->set_location($cat->get_name());
		$location->set_application(Weblcms :: APPLICATION_NAME);
		$location->set_type('category');
		$location->set_identifier($cat->get_id());
		$location->set_left_value('1');
		$location->set_right_value('2');
		$location->set_parent($root_location->get_id());
		
		if (!$location->create())
		{
			return false;
		}
	
		//creating Projects
		$cat = new CourseCategory();
		$cat->set_name('Projects');
		$cat->set_parent('0');
		$cat->set_display_order(1);
		if (!$cat->create())
		{
			return false;
		}
		
		$location = new Location();
		$location->set_location($cat->get_name());
		$location->set_application(Weblcms :: APPLICATION_NAME);
		$location->set_type('category');
		$location->set_identifier($cat->get_id());
		$location->set_left_value('1');
		$location->set_right_value('2');
		$location->set_parent($root_location->get_id());
		
		if (!$location->create())
		{
			return false;
		}		
		
		return true;
	}
	
	function create_weblcms_rights_location()
	{
		$location = new Location();
		
		$location->set_location(Weblcms :: APPLICATION_NAME);
		$location->set_application(Weblcms :: APPLICATION_NAME);
		$location->set_type('root');
		$location->set_identifier('0');
		$location->set_left_value('1');
		$location->set_right_value('2');
		$location->set_parent('0');
		
		if ($location->create())
		{
			return $location;
		}
		else
		{
			return false;
		}
	}
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>