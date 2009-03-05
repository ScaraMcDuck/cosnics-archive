<?php
/**
 * @package webservices.install
 */
require_once dirname(__FILE__).'/../lib/webservice_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';
/**
 * This installer can be used to create the storage structure for the
 * webservice application.
 */
class WebserviceInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function WebserviceInstaller($values)
    {
    	parent :: __construct($values, WebserviceDataManager :: get_instance());
    }
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>
