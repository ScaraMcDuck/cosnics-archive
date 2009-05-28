<?php
/**
 * distribute.install
 */

require_once dirname(__FILE__).'/../distribute_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';

/**
 * This installer can be used to create the storage structure for the
 * distribute application.
 * @author Hans De Bisschop
 */
class DistributeInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function DistributeInstaller($values)
    {
    	parent :: __construct($values, DistributeDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>