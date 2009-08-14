<?php
/**
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../lib/rights_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';

require_once Path :: get_rights_path() . 'lib/rights_template.class.php';
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
		if (!$this->create_default_rights_templates_and_rights())
		{
			return false;
		}
		
		return true;
	}
	
	function create_default_rights_templates_and_rights()
	{
		$rights_template = new RightsTemplate();
		$rights_template->set_name('Anonymous');
		if (!$rights_template->create())
		{
			return false;
		}
		
		$rights_template = new RightsTemplate();
		$rights_template->set_name('Administrator');
		if (!$rights_template->create())
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