<?php
/**
 * @package group.install
 */
require_once dirname(__FILE__).'/../lib/group_data_manager.class.php';
require_once dirname(__FILE__).'/../lib/group.class.php';
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
    
	/**
	 * Additional installation steps.
	 */
	function install_extra()
	{
		if (!$this->create_root_group())
		{
			return false;
		}
		
		return true;
	}
	
	function create_root_group()
	{
		$values = $this->get_form_values();
		
		$group = new Group();
		$group->set_name($values['organization_name']);
		$group->set_parent(0);
		$group->create();
		
		return true;
	}
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>