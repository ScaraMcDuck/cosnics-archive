<?php
/**
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../weblcms_manager/weblcms.class.php';
require_once dirname(__FILE__).'/../weblcms_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';

require_once 'Tree/Tree.php';

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
		
		return true;
	}
	
	function create_default_categories_in_weblcms()
	{
		$application = $this->get_application();
		$tree = RightsUtilities :: get_tree($application);
		$root = $tree->getFirstRoot();
		$root = $root['id'];
		
		//Creating Language Skills
		$cat = new CourseCategory();
		$cat->set_name('Language skills');
		$cat->set_parent('0');
		$cat->set_display_order(1);
		if (!$cat->create())
		{
			return false;
		}
		
		$element = $tree->add( array(
						'name'	=>	$cat->get_name(),
						'application' => $application,
						'type' => 'category',
						'identifier' => $cat->get_id(),
					), $root);
	
		//creating PC Skills
		$cat = new CourseCategory();
		$cat->set_name('PC skills');
		$cat->set_parent('0');
		$cat->set_display_order(1);
		if (!$cat->create())
		{
			return false;
		}
		
		$element = $tree->add( array(
						'name'	=>	$cat->get_name(),
						'application' => $application,
						'type' => 'category',
						'identifier' => $cat->get_id(),
					), $root, $element);
	
		//creating Projects
		$cat = new CourseCategory();
		$cat->set_name('Projects');
		$cat->set_parent('0');
		$cat->set_display_order(1);
		if (!$cat->create())
		{
			return false;
		}
		
		$element = $tree->add( array(
						'name'	=>	$cat->get_name(),
						'application' => $application,
						'type' => 'category',
						'identifier' => $cat->get_id(),
					), $root, $element);
		
		return true;
	}
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>