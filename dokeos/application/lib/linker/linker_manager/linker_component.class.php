<?php
/**
 * @package application.lib.linker.linker_manager
 */
abstract class LinkerComponent {

	/**
	 * The number of components allready instantiated
	 */
	private static $component_count = 0;
	/**
	 * The linker in which this componet is used
	 */
	private $linker;
	/**
	 * The id of this component
	 */
	private $id;
	/**
	 * Constructor
	 * @param Linker $linker The linker which
	 * provides this component
	 */
	protected function LinkerComponent($linker) 
	{
		$this->pm = $linker;
		$this->id =  ++self :: $component_count;
	}
	
	/**
	 * @see Linker :: redirect()
	 */
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
	}

	/**
	 * @see Linker :: get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	/**
	 * @see Linker :: get_parameters()
	 */
	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}
	
	/**
	 * @see Linker :: set_parameter()
	 */
	function set_parameter($name, $value)
	{
		return $this->get_parent()->set_parameter($name, $value);
	}
	
	/**
	 * @see Linker :: get_url()
	 */
	function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
	{
		return $this->get_parent()->get_url($parameters, $encode, $filter, $filterOn);
	}
	/**
	 * @see Linker :: display_header()
	 */
	function display_header($breadcrumbtrail, $display_search = false)
	{
		return $this->get_parent()->display_header($breadcrumbtrail, $display_search);
	}
	
	/**
	 * @see Linker :: display_message()
	 */
	function display_message($message)
	{
		return $this->get_parent()->display_message($message);
	}
	
	/**
	 * @see Linker :: display_error_message()
	 */
	function display_error_message($message)
	{
		return $this->get_parent()->display_error_message($message);
	}
	
	/**
	 * @see Linker :: display_warning_message()
	 */
	function display_warning_message($message)
	{
		return $this->get_parent()->display_warning_message($message);
	}
	
	/**
	 * @see Linker :: display_footer()
	 */
	function display_footer()
	{
		return $this->get_parent()->display_footer();
	}
	
	/**
	 * @see Linker :: display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	
	/**
	 * @see Linker :: display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	
	/**
	 * @see Linker :: display_popup_form
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	
	/**
	 * @see Linker :: get_parent
	 */
	function get_parent()
	{
		return $this->pm;
	}
	
	/**
	 * @see Linker :: get_web_code_path
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	
	/**
	 * @see Linker :: get_user()
	 */
	function get_user()
	{
		return $this->get_parent()->get_user();
	}
	
	/**
	 * @see Linker :: get_user_id()
	 */
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	
	function count_links($condition)
	{
		return $this->get_parent()->count_links($condition);
	}
	
	function retrieve_links($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_links($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_link($id)
	{
		return $this->get_parent()->retrieve_link($id);
	}
	
	// Url Creation
	
	function get_create_link_url()
	{
		return $this->get_parent()->get_create_link_url();
	}
	
	function get_update_link_url($link)
	{
		return $this->get_parent()->get_update_link_url($link);
	}
	
 	function get_delete_link_url($link)
	{
		return $this->get_parent()->get_delete_link_url($link);
	}
	
	/**
	 * Create a new profile component
	 * @param string $type The type of the component to create.
	 * @param Profile $linker The pm in
	 * which the created component will be used
	 */
	static function factory($type, $linker)
	{
		$filename = dirname(__FILE__).'/component/' . DokeosUtilities :: camelcase_to_underscores($type) . '.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'Linker'.$type.'Component';
		require_once $filename;
		return new $class($linker);
	}
}
?>