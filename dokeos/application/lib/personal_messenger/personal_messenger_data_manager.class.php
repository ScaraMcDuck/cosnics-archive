<?php
/**
 * @package application.lib.personal_messenger
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
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
	
	/**
	 * Returns whether a given object id is published in this application 
	 * @param int $object_id
	 * @return boolean Is the object is published
	 */
	abstract function learning_object_is_published($object_id);
	
	/**
	 * Gets the publication attributes of a given learning object id
	 * @param int $object_id The object id
	 * @param string $type Type of retrieval
	 * @param int $offset
	 * @param int $count
	 * @param int $order_property
	 * @param int $order_direction
	 * @return LearningObjectPublicationAttribute
	 */
	abstract function get_learning_object_publication_attribute($object_id);
	
	/**
	 * Gets the publication attributes of a given array of learning object id's
	 * @param array $object_id The array of object ids
	 * @param string $type Type of retrieval
	 * @param int $offset
	 * @param int $count
	 * @param int $order_property
	 * @param int $order_direction
	 * @return array An array of Learing Object Publication Attributes
	 */
	abstract function get_learning_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	
	/**
	 * Counts the publication attributes
	 * @param string $type Type of retrieval
	 * @param Condition $conditions
	 * @return int
	 */
	abstract function count_publication_attributes($user, $type = null, $condition = null);
    
    /**
     * Get the next available personal message publication ID
     * @return int
     */
    abstract function get_next_personal_message_publication_id();
    
    abstract function initialize();
    
	/**
	 * Count the publications
	 * @param Condition $condition
	 * @return int
	 */	
    abstract function count_personal_message_publications($condition = null);
    
	/**
	 * Count the unread publications
	 * @return int
	 */	
	abstract function count_unread_personal_message_publications($user);

	/**
	 * Retrieve a personal message publication
	 * @param int $id
	 * @return PersonalMessagePublication
	 */	
	abstract function retrieve_personal_message_publication($id);
    
	/**
	 * Retrieve a series of personal message publications 
	 * @param Condition $condition
	 * @param array $order_by
	 * @param array $order_dir
	 * @param int $offset
	 * @param int $max_objects
	 * @return PersonalMessagePublicationResultSet
	 */	
    abstract function retrieve_personal_message_publications($condition = null, $order_by = array (), $order_dir = array (), $offset = 0, $max_objects = -1);
    
	/**
	 * Update the publication
	 * @param PersonalMessagePublication $personal_message_publication
	 * @return boolean
	 */	
    abstract function update_personal_message_publication($personal_message_publication);

	/**
	 * Delete the publication
	 * @param PersonalMessagePublication $personal_message_publication
	 * @return boolean
	 */	    
    abstract function delete_personal_message_publication($personal_message_publication);
    
	/**
	 * Delete the publications
	 * @param Array $object_id An array of publication ids
	 * @return boolean
	 */	
    abstract function delete_personal_message_publications($object_id);
    
	/**
	 * Update the publication id
	 * @param LearningObjectPublicationAttribure $publication_attr
	 * @return boolean
	 */	
    abstract function update_personal_message_publication_id($publication_attr);
    
	/**
	 * Create a publication
	 * @param PersonalMessagePublication $publication
	 * @return boolean
	 */	
    abstract function create_personal_message_publication($publication);

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