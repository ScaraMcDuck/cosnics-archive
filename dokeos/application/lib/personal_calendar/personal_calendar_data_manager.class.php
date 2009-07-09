<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
/**
 * This abstract class provides the necessary functionality to connect a
 * personal calendar to a storage system.
 */
abstract class PersonalCalendarDataManager
{
    /**
	 * Instance of the class, for the singleton pattern.
	 */
	private static $instance;
	/**
	 * Constructor. Initializes the data manager.
	 */
	protected function PersonalCalendarDataManager()
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
			$class = $type.'PersonalCalendarDataManager';
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
    abstract function get_next_calendar_event_publication_id();
	/**
	 * Retrieve a profile publication
	 * @param int $id
	 * @return ProfilePublication
	 */	
	abstract function retrieve_calendar_event_publication($id);
    /**
	 * Retrieve a series of profile publications 
	 * @param Condition $condition
	 * @param array $order_by
	 * @param array $order_dir
	 * @param int $offset
	 * @param int $max_objects
	 * @return ProfilePublicationResultSet
	 */	
    abstract function retrieve_calendar_event_publications($condition = null, $order_by = array (), $order_dir = array (), $offset = 0, $max_objects = -1);
    /**
	 * Update the publication
	 * @param ProfilePublication $profile_publication
	 * @return boolean
	 */	
    abstract function update_calendar_event_publication($publication);
    /**
	 * Delete the publication
	 * @param ProfilePublication $profile_publication
	 * @return boolean
	 */	
    abstract function delete_calendar_event_publication($publication);
    /**
	 * Delete the publications
	 * @param Array $object_id An array of publication ids
	 * @return boolean
	 */	
    abstract function delete_calendar_event_publications($object_id);
    /**
	 * Update the publication id
	 * @param LearningObjectPublicationAttribure $publication_attr
	 * @return boolean
	 */	
    abstract function update_calendar_event_publication_id($publication_attr);
    /**
	 * Create a publication
	 * @param PersonalMessagePublication $publication
	 * @return boolean
	 */
    abstract function create_calendar_event_publication($publication);
    
    abstract function retrieve_calendar_event_publication_target_groups($calendar_event_publication);
    
    abstract function retrieve_calendar_event_publication_target_users($calendar_event_publication);
}
?>