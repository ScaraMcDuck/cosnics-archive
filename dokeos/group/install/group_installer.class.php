<?php
/**
 * @package group.install
 */
require_once dirname(__FILE__).'/../lib/group_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';
/**
 * This installer can be used to create the storage structure for the
 * group application.
 */
class GroupInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function GroupInstaller($values)
    {
    	parent :: __construct($values, GroupDataManager :: get_instance());
    }
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>