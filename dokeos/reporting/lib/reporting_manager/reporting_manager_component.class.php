<?php

/**
 * Base class for a webservice manager component.
 * A webservice manager provides different tools to the end user. Each tool is
 * represented by a webservice manager component and should extend this class.
 */
 /**
 * @author Michael Kyndt
 */

abstract class ReportingManagerComponent {
	/**
	 * The number of components already instantiated
	 */
	private static $component_count = 0;
	/**
	 * The user manager in which this component is used
	 */
	private $reporting_manager;
	/**
	 * The id of this component
	 */
	private $id;
	
	/**
	 * Constructor
	 * @param GroupsManager $groups_manager The user manager which
	 * provides this component
	 */
    function ReportingManagerComponent($reporting_manager) 
    {
    	$this->reporting_manager = $reporting_manager;
		$this->id =  ++self :: $component_count;
    }
    
    /**
	 * @see WebserviceManager::display_header()
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
		return $this->reporting_manager;
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
	
	function retrieve_webservice_category($id)
	{
		return $this->get_parent()->retrieve_webservice_category($id);
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
	static function factory($type, $user_manager)
	{
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'ReportingManager'.$type.'Component';
		require_once $filename;
		return new $class($user_manager);
	}
	
	function get_manage_roles_url($webservice)
	{
		return $this->get_parent()->get_manage_roles_url($webservice);
	}
}
?>