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
	
	function redirect($type = 'url', $message = null, $error_message = false, $extra_params = null)
	{
		return $this->get_parent()->redirect($type, $message, $error_message, $extra_params);
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
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php';
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
	
	function set_parameter($name, $value)
	{
		return $this->get_parent()->set_parameter($name, $value);
	}
	
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	
	/**
	 * @see Admin :: get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	/**
	 * @see Admin :: get_parameters()
	 */
	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}
	
	/**
	 * @see Admin :: retrieve_system_announcement_publication()
	 */
	function retrieve_system_announcement_publication($id)
	{
		return $this->get_parent()->retrieve_system_announcement_publication($id);
	}
	
	/**
	 * @see Admin :: retrieve_system_announcement_publications()
	 */
	function retrieve_system_announcement_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->get_parent()->retrieve_system_announcement_publications($condition, $orderBy, $orderDir, $offset, $maxObjects);
	}
	
	/**
	 * @see Admin :: count_system_announcement_publications()
	 */
	function count_system_announcement_publications($condition = null)
	{
		return $this->get_parent()->count_system_announcement_publications($condition);
	}
	
	function get_system_announcement_publication_deleting_url($system_announcement_publication)
	{
		return $this->get_parent()->get_system_announcement_publication_deleting_url($system_announcement_publication);
	}
	
	function get_system_announcement_publication_visibility_url($system_announcement_publication)
	{
		return $this->get_parent()->get_system_announcement_publication_visibility_url($system_announcement_publication);
	}
	
	function get_system_announcement_publication_viewing_url($system_announcement_publication)
	{
		return $this->get_parent()->get_system_announcement_publication_viewing_url($system_announcement_publication);
	}
	
	function get_system_announcement_publication_editing_url($system_announcement_publication)
	{
		return $this->get_parent()->get_system_announcement_publication_editing_url($system_announcement_publication);
	}	
	
	function get_system_announcement_publication_creating_url()
	{
		return $this->get_parent()->get_system_announcement_publication_creating_url();
	}
	
	/**
	 * @see Admin :: not_allowed()
	 */
	function not_allowed()
	{
		$this->get_parent()->not_allowed();
	}
}
?>