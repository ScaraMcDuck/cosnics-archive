<?php
/**
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../lib/rightsdatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';
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
	
	function get_path()
	{
		return dirname(__FILE__);
	}	
}
?>