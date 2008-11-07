<?php
/**
 * @package user.groupsmanager
 */
/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */

abstract class GroupManagerComponent {
	/**
	 * The number of components already instantiated
	 */
	private static $component_count = 0;
	/**
	 * The user manager in which this component is used
	 */
	private $group_manager;
	/**
	 * The id of this component
	 */
	private $id;
	
	/**
	 * Constructor
	 * @param GroupsManager $groups_manager The user manager which
	 * provides this component
	 */
    function GroupManagerComponent($group_manager) {
    	$this->group_manager = $group_manager;
		$this->id =  ++self :: $component_count;
    }
    
    /**
	 * @see GroupsManager::display_header()
	 */
	function display_header($breadcrumbtrail, $display_search = false)
	{
		$this->get_parent()->display_header($breadcrumbtrail, $display_search);
	}
	
	/**
	 * @see GroupsManager::display_footer()
	 */
	function display_footer()
	{
		$this->get_parent()->display_footer();
	}
	
	/**
	 * @see GroupsManager::display_message()
	 */
	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}
	
	/**
	 * @see GroupsManager::display_error_message()
	 */
	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}
	
	/**
	 * @see GroupsManager::display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	
	/**
	 * @see GroupsManager::display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	
	function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_groups($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_group_rel_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_group_rel_users($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_group_rel_user($user_id, $group_id)
	{
		return $this->get_parent()->retrieve_group_rel_user($user_id, $group_id);
	}
	
	function count_groups($conditions = null)
	{
		return $this->get_parent()->count_groups($conditions);
	}
	
	function count_group_rel_users($conditions = null)
	{
		return $this->get_parent()->count_group_rel_users($conditions);
	}
	
	/**
	 * @see GroupsManager::display_popup_form()
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	
	function get_search_condition()
	{
		return $this->get_parent()->get_search_condition();
	}
	
	function get_user_search_condition()
	{
		return $this->get_parent()->get_user_search_condition();
	}
	
	function display_user_search_form()
	{
		return $this->get_parent()->display_user_search_form();
	}
	
	/**
	 * Retrieve the user manager in which this component is active
	 * @return GroupsManager
	 */
	function get_parent()
	{
		return $this->group_manager;
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
	
	function retrieve_group($id)
	{
		return $this->get_parent()->retrieve_group($id);
	}
	
	/**
	 * @see GroupsManager::get_parameters()
	 */
	function get_parameters($include_search = false)
	{
		return $this->get_parent()->get_parameters($include_search);
	}
	
	/**
	 * @see GroupsManager::get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	/**
	 * @see GroupsManager::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->get_parent()->set_parameter($name, $value);
	}
	
	function get_search_validate()
	{
		return $this->get_parent()->get_search_validate();
	}
	
	/**
	 * @see GroupsManager::force_menu_url()
	 */
	function force_menu_url($url)
	{
		return $this->get_parent()->force_menu_url($url);
	}
	
	/**
	 * @see GroupsManager::get_url()
	 */
	function get_url($additional_parameters = array(), $include_search = false, $encode_entities = false, $include_user_search = false)
	{
		return $this->get_parent()->get_url($additional_parameters, $include_search, $encode_entities, $include_user_search);
	}
	
	/**
	 * @see GroupsManager::get_link()
	 */
	function get_link($parameters = array (), $encode = false)
	{
		return $this->get_parent()->get_link($parameters, $encode);
	}
	
	/**
	 * @see GroupsManager::redirect()
	 */
	function redirect($type = 'url', $message = null, $error_message = false, $extra_params = null)
	{
		return $this->get_parent()->redirect($type, $message, $error_message, $extra_params);
	}
	
	function get_group_editing_url($group)
	{
		return $this->get_parent()->get_group_editing_url($group);
	}
	
	function get_create_group_url($parent)
	{
		return $this->get_parent()->get_create_group_url($parent);
	}
	
	function get_group_emptying_url($group)
	{
		return $this->get_parent()->get_group_emptying_url($group);
	}
	
	function get_group_viewing_url($group)
	{
		return $this->get_parent()->get_group_viewing_url($group);
	}
	
	function get_group_roles_url($group)
	{
		return $this->get_parent()->get_group_roles_url($group);
	}
	
	function get_group_rel_user_unsubscribing_url($groupreluser)
	{
		return $this->get_parent()->get_group_rel_user_unsubscribing_url($groupreluser);
	}
	
	function get_group_rel_user_subscribing_url($group, $user)
	{
		return $this->get_parent()->get_group_rel_user_subscribing_url($group, $user);
	}
	
	function get_group_suscribe_user_browser_url($group)
	{
		return $this->get_parent()->get_group_suscribe_user_browser_url($group);
	}
	
	function get_group_delete_url($group)
	{
		return $this->get_parent()->get_group_delete_url($group);
	}
	
	function get_import_url()
	{
		return $this->get_parent()->get_import_url();
	}
	
	function get_move_group_url($group)
	{
		return $this->get_parent()->get_move_group_url($group);
	}
	
	function get_export_url()
	{
		return $this->get_parent()->get_export_url();
	}
	
	/**
	 * @see GroupsManager::get_path()
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	/**
	 * @see GroupsManager::not_allowed()
	 */
	function not_allowed()
	{
		$this->get_parent()->not_allowed();
	}
	/**
	 * Create a new user manager component
	 * @param string $type The type of the component to create.
	 * @param GroupsManager $groups_manager The user manager in
	 * which the created component will be used
	 */
	static function factory($type, $group_manager)
	{
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'GroupManager'.$type.'Component';
		require_once $filename;
		return new $class($group_manager);
	}
}
?>