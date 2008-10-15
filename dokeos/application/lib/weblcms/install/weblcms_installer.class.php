<?php
/**
 * @package application.weblcms
 */
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
		if (!$this->create_default_categories_in_weblcms())
		{
			return false;
		}
		else
		{
			$this->add_message(self :: TYPE_NORMAL, Translation :: get('DefaultWeblcmsCategoriesCreated'));
		}
		
		return true;
	}
	
	function create_default_categories_in_weblcms()
	{
		//Creating Language Skills
		$cat = new CourseCategory();
		$cat->set_name('Language skills');
		//$cat->set_code('LANG');
		$cat->set_parent('0');
		//$cat->set_tree_pos('1');
		//$cat->set_children_count('0');
		//$cat->set_auth_course_child('1');
		//$cat->set_auth_cat_child('1');
		$cat->set_display_order(1);
		if (!$cat->create())
		{
			return false;
		}
	
		//creating PC Skills
		$cat = new CourseCategory();
		$cat->set_name('PC skills');
		//$cat->set_code('PC');
		$cat->set_parent('0');
		//$cat->set_tree_pos('2');
		//$cat->set_children_count('0');
		//$cat->set_auth_course_child('1');
		//$cat->set_auth_cat_child('1');
		$cat->set_display_order(1);
		if (!$cat->create())
		{
			return false;
		}
	
		//creating Projects
		$cat = new CourseCategory();
		$cat->set_name('Projects');
		//$cat->set_code('PROJ');
		$cat->set_parent('0');
		//$cat->set_tree_pos('3');
		//$cat->set_children_count('0');
		//$cat->set_auth_course_child('1');
		//$cat->set_auth_cat_child('1');
		$cat->set_display_order(1);
		if (!$cat->create())
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