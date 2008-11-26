<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
abstract class ProfilerComponent {

	/**
	 * The number of components allready instantiated
	 */
	private static $component_count = 0;
	/**
	 * The profile in which this componet is used
	 */
	private $pm;
	/**
	 * The id of this component
	 */
	private $id;
	/**
	 * Constructor
	 * @param Profile $pm The profile which
	 * provides this component
	 */
	protected function ProfilerComponent($pm) {
		$this->pm = $pm;
		$this->id =  ++self :: $component_count;
	}
	
	/**
	 * @see ProfileManager :: redirect()
	 */
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
	}

	/**
	 * @see ProfileManager :: get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	/**
	 * @see ProfileManager :: get_parameters()
	 */
	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}
	
	/**
	 * @see ProfileManager :: set_parameter()
	 */
	function set_parameter($name, $value)
	{
		return $this->get_parent()->set_parameter($name, $value);
	}
	
	/**
	 * @see ProfileManager :: get_url()
	 */
	function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
	{
		return $this->get_parent()->get_url($parameters, $encode, $filter, $filterOn);
	}
	/**
	 * @see ProfileManager :: display_header()
	 */
	function display_header($breadcrumbtrail, $display_search = false)
	{
		return $this->get_parent()->display_header($breadcrumbtrail, $display_search);
	}
	
	/**
	 * @see ProfileManager :: display_message()
	 */
	function display_message($message)
	{
		return $this->get_parent()->display_message($message);
	}
	
	/**
	 * @see ProfileManager :: display_error_message()
	 */
	function display_error_message($message)
	{
		return $this->get_parent()->display_error_message($message);
	}
	
	/**
	 * @see ProfileManager :: display_warning_message()
	 */
	function display_warning_message($message)
	{
		return $this->get_parent()->display_warning_message($message);
	}
	
	/**
	 * @see ProfileManager :: display_footer()
	 */
	function display_footer()
	{
		return $this->get_parent()->display_footer();
	}
	
	/**
	 * @see ProfileManager :: display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	
	/**
	 * @see ProfileManager :: display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	
	/**
	 * @see ProfileManager :: display_popup_form
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	
	/**
	 * @see ProfileManager :: get_parent
	 */
	function get_parent()
	{
		return $this->pm;
	}
	
	/**
	 * @see ProfileManager :: get_web_code_path
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	
	/**
	 * @see ProfileManager :: count_profile_publications
	 */
	function count_profile_publications($condition = null)
	{
		return $this->get_parent()->count_profile_publications($condition);
	}
	
	/**
	 * @see ProfileManager :: retrieve_profile_publication()
	 */
	function retrieve_profile_publication($id)
	{
		return $this->get_parent()->retrieve_profile_publication($id);
	}
	
	/**
	 * @see ProfileManager :: retrieve_profile_publications()
	 */
	function retrieve_profile_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->get_parent()->retrieve_profile_publications($condition, $orderBy, $orderDir, $offset, $maxObjects);
	}
	
	/**
	 * @see ProfileManager :: get_user()
	 */
	function get_user()
	{
		return $this->get_parent()->get_user();
	}
	
	/**
	 * @see ProfileManager :: get_user_id()
	 */
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}

	/**
	 * @see ProfileManager :: get_search_condition()
	 */
	function get_search_condition()
	{
		return $this->get_parent()->get_search_condition();
	}
	
	/**
	 * @see ProfileManager :: get_publication_deleting_url() 
	 */
	function get_publication_deleting_url($profile)
	{
		return $this->get_parent()->get_publication_deleting_url($profile);
	}
	
	/**
	 * @see ProfileManager :: get_publication_editing_url() 
	 */
	function get_publication_editing_url($profile)
	{
		return $this->get_parent()->get_publication_editing_url($profile);
	}
	
	/**
	 * @see ProfileManager :: get_publication_viewing_url()
	 */
	function get_publication_viewing_url($profile)
	{
		return $this->get_parent()->get_publication_viewing_url($profile);
	}
	
	/**
	 * @see ProfileManager :: get_profile_creation_url()
	 */
	function get_profile_creation_url()
	{
		return $this->get_parent()->get_profile_creation_url();
	}
	
	/**
	 * @see ProfileManager :: get_publication_reply_url()
	 */
	function get_publication_reply_url($profile)
	{
		return $this->get_parent()->get_publication_reply_url($profile);
	}
	
	function get_profiler_category_manager_url()
	{
		return $this->get_parent()->get_profiler_category_manager_url();
	}
	
	/**
	 * Create a new profile component
	 * @param string $type The type of the component to create.
	 * @param Profile $pm The pm in
	 * which the created component will be used
	 */
	static function factory($type, $pm)
	{
		$filename = dirname(__FILE__).'/component/' . DokeosUtilities :: camelcase_to_underscores($type) . '.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'Profiler'.$type.'Component';
		require_once $filename;
		return new $class($pm);
	}
}
?>