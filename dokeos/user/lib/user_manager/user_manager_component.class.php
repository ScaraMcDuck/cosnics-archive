<?php
/**
 * @package users.lib.usermanager
 */
/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */

abstract class UserManagerComponent {
	/**
	 * The number of components already instantiated
	 */
	private static $component_count = 0;
	/**
	 * The user manager in which this component is used
	 */
	private $user_manager;
	/**
	 * The id of this component
	 */
	private $id;

	/**
	 * Constructor
	 * @param UserManager $user_manager The user manager which
	 * provides this component
	 */
    function UserManagerComponent($user_manager) {
    	$this->user_manager = $user_manager;
		$this->id =  ++self :: $component_count;
    }

    /**
	 * @see UserManager::display_header()
	 */
	function display_header($breadcrumbtrail, $display_search = false)
	{
		$this->get_parent()->display_header($breadcrumbtrail, $display_search);
	}

	/**
	 * @see UserManager::display_footer()
	 */
	function display_footer()
	{
		$this->get_parent()->display_footer();
	}

	/**
	 * @see UserManager::display_message()
	 */
	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}

	/**
	 * @see UserManager::display_error_message()
	 */
	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}

	/**
	 * @see UserManager::display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}

	/**
	 * @see UserManager::display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
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
	 * @see UserManager::display_popup_form()
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}

	/**
	 * @see UserManager::get_search_condition()
	 */
	function get_search_condition()
	{
		return $this->get_parent()->get_search_condition();
	}

	/**
	 * Retrieve the user manager in which this component is active
	 * @return UserManager
	 */
	function get_parent()
	{
		return $this->user_manager;
	}

	/**
	 * Retrieve the component id
	 */
	function get_component_id()
	{
		return $this->id;
	}

	/**
	 * @see UserManager::get_user
	 */
	function get_user()
	{
		return $this->get_parent()->get_user();
	}

	/**
	 * @see UserManager::get_user_id()
	 */
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}

	/**
	 * @see UserManager::retrieve_user()
	 */
	function retrieve_user($id)
	{
		return $this->get_parent()->retrieve_user($id);
	}

	/**
	 * @see UserManager::get_parameters()
	 */
	function get_parameters($include_search = false)
	{
		return $this->get_parent()->get_parameters($include_search);
	}

	/**
	 * @see UserManager::get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}

	/**
	 * @see UserManager::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->get_parent()->set_parameter($name, $value);
	}

	/**
	 * @see UserManager::get_search_parameter()
	 */
	function get_search_parameter($name)
	{
		return $this->get_parent()->get_search_parameter($name);
	}

	/**
	 * @see UserManager::get_search_validate()
	 */
	function get_search_validate()
	{
		return $this->get_parent()->get_search_validate();
	}

	/**
	 * @see UserManager::force_menu_url()
	 */
	function force_menu_url($url)
	{
		return $this->get_parent()->force_menu_url($url);
	}

	/**
	 * @see UserManager::get_url()
	 */
	function get_url($additional_parameters = array(), $include_search = false, $encode_entities = false)
	{
		return $this->get_parent()->get_url($additional_parameters, $include_search, $encode_entities);
	}

	/**
	 * @see UserManager::get_link()
	 */
	function get_link($parameters = array (), $encode = false)
	{
		return $this->get_parent()->get_link($parameters, $encode);
	}

	/**
	 * @see UserManager::redirect()
	 */
	function redirect($type = 'url', $message = null, $error_message = false, $extra_params = null)
	{
		return $this->get_parent()->redirect($type, $message, $error_message, $extra_params);
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
	/**
	 * @see UserManager::get_web_code_path()
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	/**
	 * @see UserManager::not_allowed()
	 */
	function not_allowed()
	{
		$this->get_parent()->not_allowed();
	}
	
	function get_platform_setting($variable, $application = UserManager :: APPLICATION_NAME)
	{
		return $this->get_parent()->get_platform_setting($variable, $application);
	}
	
	/**
	 * Create a new user manager component
	 * @param string $type The type of the component to create.
	 * @param UserManager $user_manager The user manager in
	 * which the created component will be used
	 */
	static function factory($type, $user_manager)
	{
		$filename = dirname(__FILE__).'/component/'.strtolower($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'UserManager'.$type.'Component';
		require_once $filename;
		return new $class($user_manager);
	}
}
?>