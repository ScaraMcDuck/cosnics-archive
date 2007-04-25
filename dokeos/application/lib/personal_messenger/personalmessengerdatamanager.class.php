<?php

abstract class PersonalMessengerDataManager {

	private static $instance;

    protected function PersonalMessengerDataManager()
    {
		$this->initialize();
    }
    
    static function get_instance()
    {
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'PersonalMessengerDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
    }
    
    /**
	 * Determines whether any of the given learning objects has been published
	 * in this application.
	 * @param array $object_ids The Id's of the learning objects
	 * @return boolean True if at least one of the given objects is published in
	 * this application, false otherwise
	 */
	abstract function any_learning_object_is_published($object_ids);
    
    abstract function get_next_personal_message_publication_id();
    
    abstract function initialize();
    
    abstract function count_personal_message_publications($condition = null);
    
	abstract function count_unread_personal_message_publications($user);

	abstract function retrieve_personal_message_publication($id);
    
    abstract function retrieve_personal_message_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1);
    
    abstract function update_personal_message_publication($personal_message_publication);
    
    abstract function delete_personal_message_publication($personal_message_publication);

	/**
	 * Creates a storage unit
	 * @param string $name Name of the storage unit
	 * @param array $properties Properties of the storage unit
	 * @param array $indexes The indexes which should be defined in the created
	 * storage unit
	 */
	abstract function create_storage_unit($name,$properties,$indexes);
}
?>