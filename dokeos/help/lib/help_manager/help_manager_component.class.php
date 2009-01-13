<?php
/**
 * @package user.helpsmanager
 */
/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */

abstract class HelpManagerComponent {
	/**
	 * The number of components already instantiated
	 */
	private static $component_count = 0;
	/**
	 * The user manager in which this component is used
	 */
	private $help_manager;
	/**
	 * The id of this component
	 */
	private $id;
	
	/**
	 * Constructor
	 * @param HelpsManager $helps_manager The user manager which
	 * provides this component
	 */
    function HelpManagerComponent($help_manager) {
    	$this->help_manager = $help_manager;
		$this->id =  ++self :: $component_count;
    }
    
    /**
	 * @see HelpsManager::display_header()
	 */
	function display_header($breadcrumbtrail, $display_search = false)
	{
		$this->get_parent()->display_header($breadcrumbtrail, $display_search);
	}
	
	/**
	 * @see HelpsManager::display_footer()
	 */
	function display_footer()
	{
		$this->get_parent()->display_footer();
	}
	
	/**
	 * @see HelpsManager::display_message()
	 */
	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}
	
	/**
	 * @see HelpsManager::display_error_message()
	 */
	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}
	
	/**
	 * @see HelpsManager::display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	
	/**
	 * @see HelpsManager::display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	
	/**
	 * @see HelpsManager::display_popup_form()
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	
	/**
	 * Retrieve the user manager in which this component is active
	 * @return HelpsManager
	 */
	function get_parent()
	{
		return $this->help_manager;
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
	 * @see HelpsManager::get_parameters()
	 */
	function get_parameters($include_search = false)
	{
		return $this->get_parent()->get_parameters($include_search);
	}
	
	/**
	 * @see HelpsManager::get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	/**
	 * @see HelpsManager::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->get_parent()->set_parameter($name, $value);
	}
	
	/**
	 * @see HelpsManager::get_url()
	 */
	function get_url($additional_parameters = array(), $include_search = false, $encode_entities = false, $include_user_search = false)
	{
		return $this->get_parent()->get_url($additional_parameters, $include_search, $encode_entities, $include_user_search);
	}
	
	/**
	 * @see HelpsManager::get_link()
	 */
	function get_link($parameters = array (), $encode = false)
	{
		return $this->get_parent()->get_link($parameters, $encode);
	}
	
	/**
	 * @see HelpsManager::redirect()
	 */
	function redirect($type = 'url', $message = null, $error_message = false, $extra_params = null)
	{
		return $this->get_parent()->redirect($type, $message, $error_message, $extra_params);
	}
	
	/**
	 * @see HelpsManager::get_path()
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	/**
	 * @see HelpsManager::not_allowed()
	 */
	function not_allowed()
	{
		$this->get_parent()->not_allowed();
	}
	/**
	 * Create a new user manager component
	 * @param string $type The type of the component to create.
	 * @param HelpsManager $helps_manager The user manager in
	 * which the created component will be used
	 */
	static function factory($type, $help_manager)
	{
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'HelpManager'.$type.'Component';
		require_once $filename;
		return new $class($help_manager);
	}
	
	public function count_help_items($condition)
	{
		return $this->get_parent()->count_help_items($condition);
	}
	
	public function retrieve_help_items($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_help_items($condition, $offset , $count, $order_property, $order_direction);
	}
	
	public function retrieve_help_item($name, $language)
	{
		return $this->get_parent()->retrieve_help_item($name, $language);
	}
}
?>