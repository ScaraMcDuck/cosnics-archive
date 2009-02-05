<?php
abstract class LassiComponent
{
	private static $component_count = 0;
	private $lassi;
	private $id;
	/**
	 * Constructor
	 * @param Lassi $lassi The lassi which
	 * provides this component
	 */
	protected function LassiComponent($lassi) {
		$this->lassi = $lassi;
		$this->id =  ++self :: $component_count;
	}
	
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
	}
	
	function is_allowed($right, $locations = array())
	{
		return $this->get_parent()->is_allowed($right, $locations);
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
	
	function get_link($parameters = array (), $encode = false)
	{
		return $this->get_parent()->get_link($parameters, $encode);
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
	
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	
	function get_parent()
	{
		return $this->lassi;
	}
	
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	
	function get_user()
	{
		return $this->get_parent()->get_user();
	}
	
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	static function factory($type, $pm)
	{
		$filename = dirname(__FILE__).'/component/'.strtolower($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'Lassi'.$type.'Component';
		require_once $filename;
		return new $class($pm);
	}
}
?>