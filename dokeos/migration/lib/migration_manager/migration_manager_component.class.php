<?php

/**
 * @package migration.migrationmanager
 * 
 * A MigrationManagerComponent is an abstract class that represents a component that is used
 * in the migrationmanager
 *
 * @author Sven Vanpoucke
 */
abstract class MigrationManagerComponent 
{
    /** The number of components allready instantiated
	 */
	private static $component_count = 0;
	
	/**
	 * The migration manager in which this component is used
	 */
	private $migration_manager;
	
	/**
	 * The id of this component
	 */
	private $id;
	
	/**
	 * Constructor
	 * @param MigrationManager $migration_manager - The migration manager which
	 * provides this component
	 */
	protected function MigrationManagerComponent($migration_manager) 
	{
		$this->migration_manager = $migration_manager;
		$this->id =  ++self :: $component_count;
	}
	
	/** @see MigrationManager::display_header()
	 */
	function display_header($breadcrumbs = array (), $display_search = false)
	{
		$this->get_parent()->display_header($breadcrumbs, $display_search);
	}
	
	/**
	 * @see MigrationManager::display_footer()
	 */
	function display_footer()
	{
		$this->get_parent()->display_footer();
	}
	
	/**
	 * @see MigrationManager::display_message()
	 */
	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}
	
	/**
	 * @see MigrationManager::display_error_message()
	 */
	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}
	
	/**
	 * @see MigrationManager::display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	
	/**
	 * @see MigrationManager::display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	
	/**
	 * @see MigrationManager::display_popup_form()
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	
	/**
	 * Retrieve the install manager in which this component is active
	 * @return MigrationManager
	 */
	function get_parent()
	{
		return $this->migration_manager;
	}
	
	/**
	 * Retrieve the component id
	 */
	function get_component_id()
	{
		return $this->id;
	}
	
	/**
	 * @see MigrationManager::get_parameters()
	 */
	function get_parameters($include_search = false)
	{
		return $this->get_parent()->get_parameters($include_search);
	}
	
	/**
	 * @see MigrationManager::get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	/**
	 * @see MigrationManager::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->get_parent()->set_parameter($name, $value);
	}
	
	/** @see MigrationManager::not_allowed()
	 */
	function not_allowed()
	{
		$this->get_parent()->not_allowed();
	}
	
	/**
	 * @see MigarionManager::get_url()
	 */
	function get_url($additional_parameters = array(), $include_search = false, $encode_entities = false)
	{
		return $this->get_parent()->get_url($additional_parameters, $include_search, $encode_entities);
	}
	
	/**
	 * @see MigarionManager::get_user()
	 */
	function get_user()
	{
		return $this->get_parent()->get_user();
	}
	
	/**
	 * Factory pattern
	 * Create a new migration manager component
	 * @param string $type The type of the component to create.
	 * @param MigrationManager $migration_manager The migration manager in
	 * which the created component will be used
	 */
	static function factory($type, $migration_manager)
	{
		
		$filename = dirname(__FILE__).'/component/'.strtolower($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'MigrationManager'.$type.'Component';
		require_once $filename;
		return new $class($migration_manager);
	}
}
?>