<?php
/**
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';
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
		
		if (!$this->create_default_categories_in_weblcms())
		{
			return array('success' => false, 'message' => $this->retrieve_message());
		}
		else
		{
			$this->add_message(Translation :: get('DefaultWeblcmsCategoriesCreated'));
		}
		
		if(!$this->register_trackers())
		{
			return array('success' => false, 'message' => $this->retrieve_message());
		}
		
		$success_message = '<span style="color: green; font-weight: bold;">' . Translation :: get('ApplicationInstallSuccess') . '</span>';
		$this->add_message($success_message);
		return array('success' => true, 'message' => $this->retrieve_message());
	}
	
	function create_default_categories_in_weblcms()
	{
		//Creating Language Skills
		$cat = new CourseCategory();
		$cat->set_name('Language skills');
		$cat->set_code('LANG');
		$cat->set_parent('0');
		$cat->set_tree_pos('1');
		$cat->set_children_count('0');
		$cat->set_auth_course_child('1');
		$cat->set_auth_cat_child('1');
		if (!$cat->create())
		{
			return false;
		}
	
		//creating PC Skills
		$cat = new CourseCategory();
		$cat->set_name('PC skills');
		$cat->set_code('PC');
		$cat->set_parent('0');
		$cat->set_tree_pos('2');
		$cat->set_children_count('0');
		$cat->set_auth_course_child('1');
		$cat->set_auth_cat_child('1');
		if (!$cat->create())
		{
			return false;
		}
	
		//creating Projects
		$cat = new CourseCategory();
		$cat->set_name('Projects');
		$cat->set_code('PROJ');
		$cat->set_parent('0');
		$cat->set_tree_pos('3');
		$cat->set_children_count('0');
		$cat->set_auth_course_child('1');
		$cat->set_auth_cat_child('1');
		if (!$cat->create())
		{
			return false;
		}
		
		return true;
	}
}
?>