<?php
abstract class LassiDataManager
{
    /**
	 * Instance of the class, for the singleton pattern.
	 */
	private static $instance;
	/**
	 * Constructor. Initializes the data manager.
	 */
	protected function LassiDataManager()
	{
		$this->initialize();
	}
	/**
	 * Creates the shared instance of the configured data manager if
	 * necessary and returns it. Uses a factory pattern.
	 * @return PersonalCalendarDataManager The instance.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'LassiDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}
	/**
	 * Initializes the data manager.
	 */
	abstract function initialize();
	/**
	 * Creates a storage unit in the personal calendar storage system
	 * @param string $name
	 * @param array $properties
	 * @param array $indexes
	 */
	abstract function create_storage_unit($name,$properties,$indexes);
	/**
	 * @see Application::learning_object_is_published()
	 */
	abstract function learning_object_is_published($object_id);
	/**
	 * @see Application::any_learning_object_is_published()
	 */
	abstract function any_learning_object_is_published($object_ids);
	/**
	 * @see Application::get_learning_object_publication_attributes()
	 */
	abstract function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	/**
	 * @see Application::get_learning_object_publication_attribute()
	 */
	abstract function get_learning_object_publication_attribute($publication_id);
	/**
	 * @see Application::count_publication_attributes()
	 */
	abstract function count_publication_attributes($type = null, $condition = null);
	/**
	 * @see Application::delete_learning_object_publications()
	 */
	abstract function delete_learning_object_publications($object_id);
	/**
	 * @see Application::update_learning_object_publication_id()
	 */
	abstract function update_learning_object_publication_id($publication_attr);
    /**
     * Get the next available profile publication ID
     * @return int
     */
}
?>