<?php
/**
 * @package application.lib.linker.install
 */
require_once dirname(__FILE__).'/../linker_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';
/**
 * This installer can be used to create the storage structure for the
 * linker application.
 */
class LinkerInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function LinkerInstaller($values)
    {
    	parent :: __construct($values, LinkerDataManager :: get_instance());
    }
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>