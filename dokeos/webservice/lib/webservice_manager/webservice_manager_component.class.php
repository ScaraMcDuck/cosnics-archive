<?php

require_once Path :: get_library_path() . 'core_application_component.class.php';

/**
 * Base class for a webservice manager component.
 * A webservice manager provides different tools to the end user. Each tool is
 * represented by a webservice manager component and should extend this class.
 */

abstract class WebserviceManagerComponent extends CoreApplicationComponent   
{
	/**
	 * Constructor
	 * @param WebserviceManager $groups_manager The user manager which
	 * provides this component
	 */
    function WebserviceManagerComponent($webservice_manager) 
    {
    	parent :: __construct($webservice_manager);
    }
	
	function retrieve_webservice_category($id)
	{
		return $this->get_parent()->retrieve_webservice_category($id);
	}
	
	function count_webservices($conditions = null)
	{
		return $this->get_parent()->count_webservices($conditions);
	}
	
	/**
	 * Create a new user manager component
	 * @param string $type The type of the component to create.
	 * @param GroupsManager $groups_manager The user manager in
	 * which the created component will be used
	 */
	static function factory($type, $user_manager)
	{
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{            
			die('Failed to load "'.$type.'" component');
		}       
		$class = 'WebserviceManager'.$type.'Component';
		require_once $filename;
		return new $class($user_manager);
	}
	
	function get_manage_roles_url($webservice)
	{
		return $this->get_parent()->get_manage_roles_url($webservice);
	}

    function retrieve_location($location_id)
	{
		return $this->get_parent()->retrieve_location($location_id);
	}
}
?>