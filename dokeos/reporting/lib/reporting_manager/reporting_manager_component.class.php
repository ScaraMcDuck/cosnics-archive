<?php

/**
 * Base class for a reporting manager component.
 * A reporting manager provides different tools to the administrator.
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
	 * The reporting manager in which this component is used
	 */
	private $reporting_manager;
	/**
	 * The id of this component
	 */
	private $id;
	
	/**
	 * Constructor
	 * @param ReportingManager $reporting_manager The reporting manager which
	 * provides this component
	 */
    function ReportingManagerComponent($reporting_manager) 
    {
    	$this->reporting_manager = $reporting_manager;
		$this->id =  ++self :: $component_count;
    }
    
    /**
	 * @see ReportingManager::display_header()
	 */
	function display_header($breadcrumbtrail, $display_search = false)
	{
		$this->get_parent()->display_header($breadcrumbtrail, $display_search);
	}
	
	/**
	 * @see ReportingManager::display_footer()
	 */
	function display_footer()
	{
		$this->get_parent()->display_footer();
	}
	
	/**
	 * @see ReportingManager::display_message()
	 */
	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}
	
	/**
	 * @see ReportingManager::display_error_message()
	 */
	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}
	
	/**
	 * @see ReportingManager::display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	
	/**
	 * @see ReportingManager::display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	
	/**
	 * @see ReportingManager::display_popup_form()
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	
	function get_search_condition()
	{
		return $this->get_parent()->get_search_condition();
	}
	
	/**
	 * Retrieve the reporting manager in which this component is active
	 * @return ReportingManager
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

    function count_reporting_template_registrations($condition = null)
	{
        return $this->get_parent()->count_reporting_template_registrations($condition);
	}

    function retrieve_reporting_template_registrations($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
        return $this->get_parent()->retrieve_reporting_template_registrations($condition, $offset, $count, $order_property, $order_direction);
	}

    function get_reporting_template_registration_viewing_url($reporting_template_registration)
	{
		return $this->get_parent()->get_reporting_template_registration_viewing_url($reporting_template_registration);
	}
	
	/**
	 * @see ReportingManager::get_parameters()
	 */
	function get_parameters($include_search = false)
	{
		return $this->get_parent()->get_parameters($include_search);
	}
	
	/**
	 * @see ReportingManager::get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	/**
	 * @see ReportingManager::set_parameter()
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
	 * @see ReportingManager::force_menu_url()
	 */
	function force_menu_url($url)
	{
		return $this->get_parent()->force_menu_url($url);
	}
	
	/**
	 * @see ReportingManager::get_url()
	 */
	function get_url($additional_parameters = array(), $include_search = false, $encode_entities = false, $include_user_search = false)
	{
		return $this->get_parent()->get_url($additional_parameters, $include_search, $encode_entities, $include_user_search);
	}
	
	/**
	 * @see ReportingManager::get_link()
	 */
	function get_link($parameters = array (), $encode = false)
	{
		return $this->get_parent()->get_link($parameters, $encode);
	}
	
	/**
	 * @see ReportingManager::redirect()
	 */
	function redirect($type = 'url', $message = null, $error_message = false, $extra_params = null)
	{
		return $this->get_parent()->redirect($type, $message, $error_message, $extra_params);
	}
	
	/**
	 * @see ReportingManager::get_path()
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	/**
	 * @see ReportingManager::not_allowed()
	 */
	function not_allowed()
	{
		$this->get_parent()->not_allowed();
	}
	
	/**
	 * @see ReportingManager ::display_popup_form()
	 */
	function get_application_platform_admin_links()
	{
		return $this->get_parent()->get_application_platform_admin_links();
	}
	/**
	 * Create a new reporting manager component
	 * @param string $type The type of the component to create.
	 * @param ReportingManager $reporting_manager The user manager in
	 * which the created component will be used
	 */
	static function factory($type, $reporting_manager)
	{
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'ReportingManager'.$type.'Component';
		require_once $filename;
		return new $class($reporting_manager);
	}
}
?>