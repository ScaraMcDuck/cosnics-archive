<?php
/**
 * @package user.usermanager
 */
/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */

abstract class RightsManagerComponent {
	/**
	 * The number of components already instantiated
	 */
	private static $component_count = 0;
	/**
	 * The user manager in which this component is used
	 */
	private $rights_manager;
	/**
	 * The id of this component
	 */
	private $id;
	
	/**
	 * Constructor
	 * @param RightsManager $user_manager The user manager which
	 * provides this component
	 */
    function RightsManagerComponent($rights_manager) {
    	$this->rights_manager = $rights_manager;
		$this->id =  ++self :: $component_count;
    }
    
    /**
	 * @see RightsManager::display_header()
	 */
	function display_header($breadcrumbs = array (), $display_search = false)
	{
		$this->get_parent()->display_header($breadcrumbs, $display_search);
	}
	
	/**
	 * @see RightsManager::display_footer()
	 */
	function display_footer()
	{
		$this->get_parent()->display_footer();
	}
	
	/**
	 * @see RightsManager::display_message()
	 */
	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}
	
	/**
	 * @see RightsManager::display_error_message()
	 */
	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}
	
	/**
	 * @see RightsManager::display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	
	/**
	 * @see RightsManager::display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	
	function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_users($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_groups($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_roles($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_roles($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_rights($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_rights($condition, $offset, $count, $order_property, $order_direction);
	}

	function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_locations($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_role_right_location($right_id, $role_id, $location_id)
	{
		return $this->get_parent()->retrieve_role_right_location($right_id, $role_id, $location_id);
	}
	
	function retrieve_user_role($user_id, $location_id)
	{
		return $this->get_parent()->retrieve_user_role($user_id, $location_id);
	}
	
	function retrieve_group_role($group_id, $location_id)
	{
		return $this->get_parent()->retrieve_group_role($group_id, $location_id);
	}
	
	function retrieve_location($location_id)
	{
		return $this->get_parent()->retrieve_location($location_id);
	}	
	
	function count_users($conditions = null)
	{
		return $this->get_parent()->count_users($conditions);
	}
	
	function count_groups($conditions = null)
	{
		return $this->get_parent()->count_groups($conditions);
	}
	
	/**
	 * @see RightsManager::display_popup_form()
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}

	/**
	 * @see RightsManager::get_search_condition()
	 */
	function get_user_search_condition()
	{
		return $this->get_parent()->get_user_search_condition();
	}
	
	function get_group_search_condition()
	{
		return $this->get_parent()->get_group_search_condition();
	}
	
	/**
	 * Retrieve the user manager in which this component is active
	 * @return RightsManager
	 */
	function get_parent()
	{
		return $this->rights_manager;
	}
	
	/**
	 * Retrieve the component id
	 */
	function get_component_id()
	{
		return $this->id;
	}
	
	function get_user()
	{
		return $this->get_parent()->get_user();
	}
	
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	
	/**
	 * @see RightsManager::retrieve_user()
	 */
	function retrieve_user($id)
	{
		return $this->get_parent()->retrieve_user($id);
	}
	
	function retrieve_group($id)
	{
		return $this->get_parent()->retrieve_group($id);
	}
	
	/**
	 * @see RightsManager::get_parameters()
	 */
	function get_parameters($include_search = false)
	{
		return $this->get_parent()->get_parameters($include_search);
	}
	
	/**
	 * @see RightsManager::get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	/**
	 * @see RightsManager::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->get_parent()->set_parameter($name, $value);
	}
	
	function get_user_search_validate()
	{
		return $this->get_parent()->get_user_search_validate();
	}
	
	function get_group_search_validate()
	{
		return $this->get_parent()->get_group_search_validate();
	}
	
	/**
	 * @see RightsManager::force_menu_url()
	 */
	function force_menu_url($url)
	{
		return $this->get_parent()->force_menu_url($url);
	}
	
	/**
	 * @see RightsManager::get_url()
	 */
	function get_url($additional_parameters = array(), $include_search = false, $encode_entities = false)
	{
		return $this->get_parent()->get_url($additional_parameters, $include_search, $encode_entities);
	}
	
	/**
	 * @see RightsManager::get_link()
	 */
	function get_link($parameters = array (), $encode = false)
	{
		return $this->get_parent()->get_link($parameters, $encode);
	}
	
	/**
	 * @see RightsManager::redirect()
	 */
	function redirect($type = 'url', $message = null, $error_message = false, $extra_params = null)
	{
		return $this->get_parent()->redirect($type, $message, $error_message, $extra_params);
	}
	
	function is_allowed($right, $role_id, $location_id)
	{
		return $this->get_parent()->is_allowed($right, $role_id, $location_id);
	}
	
	/**
	 * @see RightsManager::User_deletion_allowed()
	 */
	function user_deletion_allowed($user)
	{
		return $this->get_parent()->user_deletion_allowed($user);
	}
	/**
	 * @see RightsManager::get_user_editing_url()
	 */
	function get_user_editing_url($user)
	{
		return $this->get_parent()->get_user_editing_url($user);
	}
	
	function get_group_editing_url($group)
	{
		return $this->get_parent()->get_group_editing_url($group);
	}
	
	/**
	 * @see RightsManager::get_user_quota_url()
	 */
	function get_user_quota_url($user)
	{
		return $this->get_parent()->get_user_quota_url($user);
	}
	
	function get_user_roles_url($user)
	{
		return $this->get_parent()->get_user_roles_url($user);
	}
	
	function get_group_roles_url($group)
	{
		return $this->get_parent()->get_group_roles_url($group);
	}
	
	/**
	 * @see RightsManager::get_path()
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	/**
	 * @see RightsManager::not_allowed()
	 */
	function not_allowed()
	{
		$this->get_parent()->not_allowed();
	}
	/**
	 * Create a new user manager component
	 * @param string $type The type of the component to create.
	 * @param RightsManager $user_manager The user manager in
	 * which the created component will be used
	 */
	static function factory($type, $user_manager)
	{
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'RightsManager'.$type.'Component';
		require_once $filename;
		return new $class($user_manager);
	}
}
?>