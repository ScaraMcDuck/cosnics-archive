<?php
/**
 * @package users.lib.usermanager
 */
require_once Path :: get_library_path() . 'core_application_component.class.php';
/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */

abstract class UserManagerComponent extends CoreApplicationComponent  
{
	/**
	 * Constructor
	 * @param UserManager $user_manager The user manager which
	 * provides this component
	 */
    function UserManagerComponent($user_manager) 
    {
    	parent :: __construct($user_manager);
    }

	/**
	 * @see UserManager::retrieve_users()
	 */
	function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_users($condition, $offset, $count, $order_property, $order_direction);
	}

	/**
	 * @see UserManager::count_users()
	 */
	function count_users($conditions = null)
	{
		return $this->get_parent()->count_users($conditions);
	}
	
	/**
	 * @see UserManager::retrieve_user()
	 */
	function retrieve_user($id)
	{
		return $this->get_parent()->retrieve_user($id);
	}

	/**
	 * @see UserManager::User_deletion_allowed()
	 */
	function user_deletion_allowed($user)
	{
		return $this->get_parent()->user_deletion_allowed($user);
	}
	/**
	 * @see UserManager::get_user_editing_url()
	 */
	function get_user_editing_url($user)
	{
		return $this->get_parent()->get_user_editing_url($user);
	}
	/**
	 * @see UserManager::get_user_quota_url()
	 */
	function get_user_quota_url($user)
	{
		return $this->get_parent()->get_user_quota_url($user);
	}
	/**
	 * @see UserManager::get_user_delete_url()
	 */
	function get_user_delete_url($user)
	{
		return $this->get_parent()->get_user_delete_url($user);
	}
	
	function get_change_user_url($user)
	{
		return $this->get_parent()->get_change_user_url($user);
	}
	
	function get_manage_roles_url($user)
	{
		return $this->get_parent()->get_manage_roles_url($user);
	}
	
	/**
	 * Create a new user manager component
	 * @param string $type The type of the component to create.
	 * @param UserManager $user_manager The user manager in
	 * which the created component will be used
	 */
	static function factory($type, $user_manager)
	{
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'UserManager'.$type.'Component';
		require_once $filename;
		return new $class($user_manager);
	}
	
	function get_create_buddylist_category_url()
	{
		return $this->get_parent()->get_create_buddylist_category_url();
	}
	
 	function get_delete_buddylist_category_url($category_id)
	{
		return $this->get_parent()->get_delete_buddylist_category_url($category_id);
	}
	
 	function get_update_buddylist_category_url($category_id)
	{
		return $this->get_parent()->get_update_buddylist_category_url($category_id);
	}
	
 	function get_create_buddylist_item_url()
	{
		return $this->get_parent()->get_create_buddylist_item_url();
	}
	
 	function get_delete_buddylist_item_url($item_id)
	{
		return $this->get_parent()->get_delete_buddylist_item_url($item_id);
	}
	
 	function get_change_buddylist_item_status_url($item_id, $status)
	{
		return $this->get_parent()->get_change_buddylist_item_status_url($item_id, $status);
	}

    function get_reporting_url($classname, $params)
    {
        return $this->get_parent()->get_reporting_url($classname, $params);
    }
}
?>