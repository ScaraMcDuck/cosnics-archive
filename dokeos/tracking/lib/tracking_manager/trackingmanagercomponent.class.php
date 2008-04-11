<?php
/**
 * @package tracking.lib.trackingmanager
 */
/**
 * Base class for a tracking manager component.
 * A tracking manager provides different tools to the end tracker. Each tool is
 * represented by a tracking manager component and should extend this class.
 */

abstract class TrackingManagerComponent 
{
	/**
	 * The number of components already instantiated
	 */
	private static $component_count = 0;
	/**
	 * The tracking manager in which this component is used
	 */
	private $tracking_manager;
	/**
	 * The id of this component
	 */
	private $id;

	/**
	 * Constructor
	 * @param trackingManager $tracking_manager The tracking manager which
	 * provides this component
	 */
    function TrackingManagerComponent($tracking_manager) 
    {
    	$this->tracking_manager = $tracking_manager;
		$this->id =  ++self :: $component_count;
    }

    /**
	 * @see trackingManager::display_header()
	 */
	function display_header($breadcrumbs, $display_search = false)
	{
		$this->get_parent()->display_header($breadcrumbs, $display_search);
	}

	/**
	 * @see trackingManager::display_footer()
	 */
	function display_footer()
	{
		$this->get_parent()->display_footer();
	}

	/**
	 * @see trackingManager::display_message()
	 */
	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}

	/**
	 * @see trackingManager::display_error_message()
	 */
	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}

	/**
	 * @see trackingManager::display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}

	/**
	 * @see trackingManager::display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}

	/**
	 * @see trackingManager::display_popup_form()
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}

	/**
	 * Retrieve the tracking manager in which this component is active
	 * @return trackingManager
	 */
	function get_parent()
	{
		return $this->tracking_manager;
	}

	/**
	 * Retrieve the component id
	 */
	function get_component_id()
	{
		return $this->id;
	}

	/**
	 * @see trackingManager::get_parameters()
	 */
	function get_parameters($include_search = false)
	{
		return $this->get_parent()->get_parameters($include_search);
	}

	/**
	 * @see trackingManager::get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}

	/**
	 * @see trackingManager::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->get_parent()->set_parameter($name, $value);
	}

	/**
	 * @see trackingManager::force_menu_url()
	 */
	function force_menu_url($url)
	{
		return $this->get_parent()->force_menu_url($url);
	}

	/**
	 * @see trackingManager::get_url()
	 */
	function get_url($additional_parameters = array(), $include_search = false, $encode_entities = false)
	{
		return $this->get_parent()->get_url($additional_parameters, $include_search, $encode_entities);
	}

	/**
	 * @see trackingManager::get_link()
	 */
	function get_link($parameters = array (), $encode = false)
	{
		return $this->get_parent()->get_link($parameters, $encode);
	}

	/**
	 * @see trackingManager::redirect()
	 */
	function redirect($type = 'url', $message = null, $error_message = false, $extra_params = null)
	{
		return $this->get_parent()->redirect($type, $message, $error_message, $extra_params);
	}

	/**
	 * @see trackingManager::get_web_code_path()
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	
	/**
	 * Create a new tracking manager component
	 * @param string $type The type of the component to create.
	 * @param trackingManager $tracking_manager The tracking manager in
	 * which the created component will be used
	 */
	static function factory($type, $tracking_manager)
	{
		$filename = dirname(__FILE__) . '/component/'.strtolower($type) . '.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "' . $type . '" component');
		}
		$class = 'TrackingManager' . $type . 'Component';
		require_once $filename;
		return new $class($tracking_manager);
	}
	
	/**
	 * Retrieves the active user
	 * @return User the active user
	 * @see TrackingManager :: get_user()
	 */
	function get_user()
	{
		return $this->get_parent()->get_user();
	}

	/**
	 * Retrieves the browser url
	 * @return the browser url
	 * @see TrackingManager :: get_browser_url()
	 */
	function get_browser_url()
	{
		return $this->get_parent()->get_browser_url();
	}
}
?>