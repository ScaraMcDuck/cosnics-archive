<?php

require_once dirname(__FILE__).'/rights_manager_component.class.php';
require_once dirname(__FILE__).'/../rights_data_manager.class.php';
require_once Path :: get_library_path() . 'core_application.class.php';

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
 class RightsManager extends CoreApplication 
 {
 	const APPLICATION_NAME = 'rights';
 	
	const PARAM_REMOVE_SELECTED = 'delete';
	const PARAM_FIRSTLETTER = 'firstletter';
	const PARAM_COMPONENT_ACTION = 'action';
	const PARAM_APPLICATION = 'application';
	
	const PARAM_ROLE_ID = 'role';
	
	const ACTION_EDIT_RIGHTS = 'edit';
	const ACTION_BROWSE_ROLES = 'browse_roles';
	const ACTION_EDIT_ROLES = 'edit_role';
	const ACTION_DELETE_ROLES = 'delete_role';
	const ACTION_CREATE_ROLE = 'create_role';
	
	private $quota_url;
	private $publication_url;
	private $create_url;
	private $recycle_bin_url;
	
    function RightsManager($user = null) 
    {
    	parent :: __construct($user);
		//$this->create_url = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_USER));   	
    }
    
    function get_application_name()
    {
    	return self :: APPLICATION_NAME;
    }
    
    /**
	 * Run this user manager
	 */
	function run()
	{
		/*
		 * Only setting breadcrumbs here. Some stuff still calls
		 * forceCurrentUrl(), but that should not affect the breadcrumbs.
		 */
		//$this->breadcrumbs = $this->get_category_menu()->get_breadcrumbs();
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_EDIT_RIGHTS :
				$component = RightsManagerComponent :: factory('Editor', $this);
				break;
			case self :: ACTION_BROWSE_ROLES :
				$component = RightsManagerComponent :: factory('RoleBrowser', $this);
				break;
			case self :: ACTION_EDIT_ROLES :
				$component = RightsManagerComponent :: factory('RoleEditor', $this);
				break;
			case self :: ACTION_CREATE_ROLE :
				$component = RightsManagerComponent :: factory('RoleCreator', $this);
				break;
			case self :: ACTION_DELETE_ROLES :
				$component = RightsManagerComponent :: factory('RoleDeleter', $this);
				break;
			default :
				$this->set_action(self :: ACTION_EDIT_RIGHTS);
				$component = RightsManagerComponent :: factory('Editor', $this);
		}
		$component->run();
	}
	
	function retrieve_roles($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return RightsDataManager :: get_instance()->retrieve_roles($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function count_roles($condition = null)
	{
		return RightsDataManager :: get_instance()->count_roles($condition);
	}
	
	function delete_role($role)
	{
		return RightsDataManager :: get_instance()->delete_role($role);
	}
	
	function retrieve_rights($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return RightsDataManager :: get_instance()->retrieve_rights($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return RightsDataManager :: get_instance()->retrieve_locations($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_role($id)
	{
		return RightsDataManager :: get_instance()->retrieve_role($id);
	}
	
	function retrieve_location($id)
	{
		return RightsDataManager :: get_instance()->retrieve_location($id);
	}
	
	function retrieve_right($id)
	{
		return RightsDataManager :: get_instance()->retrieve_right($id);
	}
	
	public function get_application_platform_admin_links()
	{
		$links		= array();
		$links[]	= array('name' => Translation :: get('Roles'),
							'description' => Translation :: get('RolesDescription'),
							'action' => 'list',
							'url' => $this->get_link(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_BROWSE_ROLES)));
		$links[]	= array('name' => Translation :: get('Rights'),
							'description' => Translation :: get('RightsDescription'),
							'action' => 'manage',
							'url' => $this->get_link(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS)));
		return array('application' => array('name' => Translation :: get('Rights'), 'class' => 'rights'), 'links' => $links, 'search' => null);
	}
	
	function get_location_id_from_short_string($location)
	{
		$location = 'platform|' . $location;
		$rdm = RightsDataManager :: get_instance();
		return $rdm->retrieve_location_id_from_location_string($location);
	}
	
	function is_allowed($right, $role_id, $location_id)
	{
		$rdm = RightsDataManager :: get_instance();
		$rolerightlocation = $rdm->retrieve_role_right_location($right, $role_id, $location_id);
		return $rolerightlocation->get_value();
	}
	
	function retrieve_role_right_location($right_id, $role_id, $location_id)
	{
		return RightsDataManager :: get_instance()->retrieve_role_right_location($right_id, $role_id, $location_id);
	}
	
	function get_role_deleting_url($role)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_ROLES, self :: PARAM_ROLE_ID => $role->get_id()));
	}
	
	function get_role_editing_url($role)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_ROLES, self :: PARAM_ROLE_ID => $role->get_id()));
	}
}
?>