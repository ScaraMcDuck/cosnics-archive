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
		return $this->pm;
	}
	
	function get_web_code_path()
	{
		return $this->get_parent()->get_web_code_path();
	}
	
	function get_search_condition()
	{
		return $this->get_parent()->get_search_condition();
	}
	
	function get_search_validate()
	{
		return $this->get_parent()->get_search_validate();
	}
	
	function get_search_parameter($name)
	{
		return $this->get_parent()->get_search_parameter($name);
	}

	function count_personal_message_publications($condition = null)
	{
		return $this->get_parent()->count_personal_message_publications($condition);
	}
	
	function retrieve_personal_message_publication($id)
	{
		return $this->get_parent()->retrieve_personal_message_publication($id);
	}
	
	function retrieve_personal_message_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->get_parent()->retrieve_personal_message_publications($condition, $orderBy, $orderDir, $offset, $maxObjects);
	}
	
	function get_user()
	{
		return $this->get_parent()->get_user();
	}
	
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	
	function get_publication_deleting_url($personal_message)
	{
		return $this->get_parent()->get_publication_deleting_url($personal_message);
	}
	
	function get_publication_viewing_url($personal_message)
	{
		return $this->get_parent()->get_publication_viewing_url($personal_message);
	}
	
	function get_personal_message_creation_url()
	{
		return $this->get_parent()->get_personal_message_creation_url();
	}
	
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