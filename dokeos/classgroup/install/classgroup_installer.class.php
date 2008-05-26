<?php
/**
 * @package classgroup.install
 */
require_once dirname(__FILE__).'/../lib/class_group_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';
/**
 * This installer can be used to create the storage structure for the
 * classgroup application.
 */
class ClassGroupInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function ClassGroupInstaller($values)
    {
    	parent :: __construct($values, ClassGroupDataManager :: get_instance());
    }
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>