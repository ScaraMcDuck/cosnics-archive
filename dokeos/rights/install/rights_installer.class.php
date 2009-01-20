<?php
/**
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../lib/rights_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';

require_once Path :: get_rights_path() . 'lib/role.class.php';
require_once Path :: get_rights_path() . 'lib/right.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * weblcms application.
 */
class RightsInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function RightsInstaller($values)
    {
    	parent :: __construct($values, RightsDataManager :: get_instance());
    }
    
	function install_extra()
	{
		if (!$this->create_default_roles_and_rights())
		{
			return false;
		}
		
		return true;
	}
	
	function create_default_roles_and_rights()
	{
		$role = new Role();
		$role->set_name('Anonymous');
		if (!$role->create())
		{
			return false;
		}
		
		$role = new Role();
		$role->set_name('Administrator');
		if (!$role->create())
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