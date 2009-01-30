<?php
require_once dirname(__FILE__).'/../lassi_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';

class LassiInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function LassiInstaller($values)
    {
		parent :: __construct($values, LassiDataManager :: get_instance());
    }
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>