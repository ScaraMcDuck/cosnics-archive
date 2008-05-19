<?php
/**
 * @package classgroup.install
 */
require_once dirname(__FILE__).'/../lib/classgroupdatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
/**
 * This installer can be used to create the storage structure for the
 * classgroup application.
 */
class ClassgroupInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function ClassgroupInstaller($values)
    {
    	parent :: __construct($values, ClassgroupDataManager :: get_instance());
    }
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>