<?php

abstract class PersonalMessengerComponent {

	/**
	 * The number of components allready instantiated
	 */
	private static $component_count = 0;
	/**
	 * The pm in which this componet is used
	 */
	private $pm;
	/**
	 * The id of this component
	 */
	private $id;
	/**
	 * Constructor
	 * @param PersonalMessage $pm The pm which
	 * provides this component
	 */
	protected function PersonalMessengerComponent($pm) {
		$this->pm = $pm;
		$this->id =  ++self :: $component_count;
	}
	
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
	}

	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}
	
	function set_parameter($name, $value)
	{
		return $this->get_parent()->set_parameter($name, $value);
	}
	
	function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
	{
		return $this->get_parent()->get_url($parameters, $encode, $filter, $filterOn);
	}
	
	function display_header($breadcrumbs = array (), $display_search = false)
	{
		return $this->get_parent()->display_header($breadcrumbs, $display_search);
	}
	
	function display_message($message)
	{
		return $this->get_parent()->display_message($message);
	}
	
	function display_error_message($message)
	{
		return $this->get_parent()->display_error_message($message);
	}
	
	function display_warning_message($message)
	{
		return $this->get_parent()->display_warning_message($message);
	}
	
	function display_footer()
	{
		return $this->get_parent()->display_footer();
	}
	
	function get_parent()
	{
		return $this->pm;
	}
	
	function get_web_code_path()
	{
		return $this->get_parent()->get_web_code_path();
	}
	
//	/**
//	 * @see Weblcms::get_search_condition()
//	 */
//	function get_search_condition()
//	{
//		return $this->get_parent()->get_search_condition();
//	}
//	
//	/**
//	 * @see Weblcms::get_search_validate()
//	 */
//	function get_search_validate()
//	{
//		return $this->get_parent()->get_search_validate();
//	}
//	
//	/**
//	 * @see Weblcms::get_search_parameter()
//	 */
//	function get_search_parameter($name)
//	{
//		return $this->get_parent()->get_search_parameter($name);
//	}
	
	/**
	 * Create a new pm component
	 * @param string $type The type of the component to create.
	 * @param PersonalMessage $pm The pm in
	 * which the created component will be used
	 */
	static function factory($type, $pm)
	{
		$filename = dirname(__FILE__).'/component/'.strtolower($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'PersonalMessenger'.$type.'Component';
		require_once $filename;
		return new $class($pm);
	}
}
?>