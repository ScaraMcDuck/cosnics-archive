<?php
/**
 * @package admin.lib.admin_manager
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
abstract class AdminComponent {

	/**
	 * The admin in which this componet is used
	 */
	private $admin;
	/**
	 * The id of this component
	 */
	private $id;
	/**
	 * Constructor
	 * @param Admin $admin The admin which
	 * provides this component
	 */
	/**
	 * The number of components allready instantiated
	 */
	private static $admin_count = 0;
	
	protected function AdminComponent($admin) {
		$this->admin = $admin;
		$this->id =  ++self :: $admin_count;
	}
	
	/**
	 * @see Admin::display_header()
	 */
	function display_header($breadcrumbs = array (), $display_search = false)
	{
		$this->get_parent()->display_header($breadcrumbs, $display_search);
	}
	/**
	 * @see Admin::display_footer()
	 */
	function display_footer()
	{
		$this->get_parent()->display_footer();
	}
	/**
	 * @see Admin::display_message()
	 */
	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}
	/**
	 * @see Admin::display_error_message()
	 */
	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}
	/**
	 * @see Admin::display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	/**
	 * @see Admin::display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	/**
	 * @see Admin::display_popup_form()
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	
	/**
	 * @see Admin::display_popup_form()
	 */
	function get_application_platform_admin_links()
	{
		return $this->get_parent()->get_application_platform_admin_links();
	}
	
	/**
	 * @see Admin::get_url()
	 */
	function get_url($additional_parameters = array(), $include_search = false, $encode_entities = false)
	{
		return $this->get_parent()->get_url($additional_parameters, $include_search, $encode_entities);
	}
	
	function get_user()
	{
		return $this->get_parent()->get_user();
	}
	
	/**
	 * Retrieve the admin in which this component is active
	 * @return Admin
	 */
	function get_parent()
	{
		return $this->admin;
	}
	
	/**
	 * Create a new admin component
	 * @param string $type The type of the component to create.
	 * @param Admin $admin The admin in
	 * which the created component will be used
	 */
	static function factory($type, $admin)
	{
		$filename = dirname(__FILE__).'/component/'.strtolower($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'Admin'.$type.'Component';
		require_once $filename;
		return new $class($admin);
	}
	
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
}
?>