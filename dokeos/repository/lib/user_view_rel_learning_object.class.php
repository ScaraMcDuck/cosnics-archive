<?php

require_once dirname(__FILE__).'/repository_data_manager.class.php';

/**
 *  @author Sven Vanpoucke
 */

class UserViewRelLearningObject
{
	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_VIEW_ID = 'view_id';
	const PROPERTY_LEARNING_OBJECT_TYPE = 'learning_object_type';
	const PROPERTY_VISIBILITY = 'visibility';
	
	/**
	 * Default properties of the user_view_rel_learning_object object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new user_view_rel_learning_object object.
	 * @param int $view_id The numeric VIEW_ID of the user_view_rel_learning_object object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the user_view_rel_learning_object
	 *                                 object. Associative array.
	 */
	function UserViewRelLearningObject($view_id = 0, $defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this user_view_rel_learning_object object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this user_view_rel_learning_object.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Get the default properties of all user_view_rel_learning_objects.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_VIEW_ID, self :: PROPERTY_VISIBILITY, self :: PROPERTY_LEARNING_OBJECT_TYPE);
	}
		
	/**
	 * Sets a default property of this user_view_rel_learning_object by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given view_identifier is the name of a default user_view_rel_learning_object
	 * property.
	 * @param string $name The view_identifier.
	 * @return boolean True if the view_identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}

	/**
	 * Returns the view_id of this user_view_rel_learning_object.
	 * @return int The view_id.
	 */
	function get_view_id()
	{
		return $this->get_default_property(self :: PROPERTY_VIEW_ID);
	}
	
	/**
	 * Returns the name of this user_view_rel_learning_object.
	 * @return String The name
	 */
	function get_visibility()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBILITY);
	}
	
	/**
	 * Sets the user_view_rel_learning_object_view_id of this user_view_rel_learning_object.
	 * @param int $user_view_rel_learning_object_view_id The user_view_rel_learning_object_view_id.
	 */
	function set_view_id($view_id)
	{
		$this->set_default_property(self :: PROPERTY_VIEW_ID, $view_id);
	}		
	
	/**
	 * Sets the name of this user_view_rel_learning_object.
	 * @param String $name the name.
	 */
	function set_visibility($visibility)
	{
		$this->set_default_property(self :: PROPERTY_VISIBILITY, $visibility);
	}
	
	function get_learning_object_type()
	{
		return $this->get_default_property(self :: PROPERTY_LEARNING_OBJECT_TYPE);
	}
	
	function set_learning_object_type($learning_object_type)
	{
		$this->set_default_property(self :: PROPERTY_LEARNING_OBJECT_TYPE, $learning_object_type);
	}
	
	function delete()
	{
		return RepositoryDataManager :: get_instance()->delete_user_view_rel_learning_object($this);
	}
	
	function create()
	{
		$gdm = RepositoryDataManager :: get_instance();
		return $gdm->create_user_view_rel_learning_object($this);
	}
	
	function update() 
	{
		$gdm = RepositoryDataManager :: get_instance();
		$success = $gdm->update_user_view_rel_learning_object($this);
		if (!$success)
		{
			return false;
		}

		return true;	
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>