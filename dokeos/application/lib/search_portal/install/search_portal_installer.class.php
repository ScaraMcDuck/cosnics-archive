<?php
/**
 * $Id:$
 * @package application.portfolio
 */
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 *      search portal application.
 */
class SearchPortalInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function SearchPortalInstaller($values)
    {
    	parent :: __construct($values);
    }
	/**
	 * Runs the install-script.
	 * @todo This function now uses the function of the RepositoryInstaller
	 * class. These shared functions should be available in a common base class.
	 */
	function install()
	{
		if(!$this->register_trackers())
		{
			return false;
		}
		
		return $this->installation_successful();
	}
}
?>