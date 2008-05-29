<?php

abstract class InstallManagerComponent {
	/**
	 * The number of components allready instantiated
	 */
	private static $component_count = 0;
	/**
	 * The install manager in which this componet is used
	 */
	private $install_manager;
	/**
	 * The id of this component
	 */
	private $id;
	/**
	 * Constructor
	 * @param InstallManager $install_manager The install manager which
	 * provides this component
	 */
	protected function InstallManagerComponent($install_manager) {
		$this->install_manager = $install_manager;
		$this->id =  ++self :: $component_count;
	}
	/**
	 * @see InstallManager::display_header()
	 */
	function display_header($breadcrumbs = array (), $display_search = false)
	{
		$this->get_parent()->display_header($breadcrumbs, $display_search);
	}
	/**
	 * @see InstallManager::display_footer()
	 */
	function display_footer()
	{
		$this->get_parent()->display_footer();
	}
	/**
	 * @see InstallManager::display_message()
	 */
	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}
	/**
	 * @see InstallManager::display_error_message()
	 */
	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}
	/**
	 * @see InstallManager::display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	/**
	 * @see InstallManager::display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	/**
	 * @see InstallManager::display_popup_form()
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	/**
	 * Retrieve the install manager in which this component is active
	 * @return InstallManager
	 */
	function get_parent()
	{
		return $this->install_manager;
	}
	/**
	 * Retrieve the component id
	 */
	function get_component_id()
	{
		return $this->id;
	}
	/**
	 * @see InstallManager::get_parameters()
	 */
	function get_parameters($include_search = false)
	{
		return $this->get_parent()->get_parameters($include_search);
	}
	/**
	 * @see InstallManager::get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	/**
	 * @see InstallManager::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->get_parent()->set_parameter($name, $value);
	}
	
	/**
	 * @see RepositoryManager::not_allowed()
	 */
	function not_allowed()
	{
		$this->get_parent()->not_allowed();
	}
	
	/**
	 * Create a new repository manager component
	 * @param string $type The type of the component to create.
	 * @param RepositoryManager $repository_manager The repository manager in
	 * which the created component will be used
	 */
	static function factory($type, $install_manager)
	{
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'InstallManager'.$type.'Component';
		require_once $filename;
		return new $class($install_manager);
	}
}
?>