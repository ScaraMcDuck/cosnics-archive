<?php

require_once dirname(__FILE__).'/repository_data_manager.class.php';

/**
 *  @author Sven Vanpoucke
 */

class UserView
{
	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_ID = 'id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';
	
	/**
	 * Default properties of the user_view object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new user_view object.
	 * @param int $id The numeric ID of the user_view object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the user_view
	 *                                 object. Associative array.
	 */
	function UserView($id = 0, $defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this user_view object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this user_view.
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
	 * Get the default properties of all user_views.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_USER_ID);
	}
		
	/**
	 * Sets a default property of this user_view by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default user_view
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}

	/**
	 * Returns the id of this user_view.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the name of this user_view.
	 * @return String The name
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	/**
	 * Returns the description of this user_view.
	 * @return String The description
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}
	
	/**
	 * Sets the user_view_id of this user_view.
	 * @param int $user_view_id The user_view_id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}		
	
	/**
	 * Sets the name of this user_view.
	 * @param String $name the name.
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	/**
	 * Sets the description of this user_view.
	 * @param String $description the description.
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	
	
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	
	function delete()
	{
		return RepositoryDataManager :: get_instance()->delete_user_view($this);
	}
	
	function create()
	{
		$gdm = RepositoryDataManager :: get_instance();
		$this->set_id($gdm->get_next_user_view_id());
		$success = $gdm->create_user_view($this);
		
		$registrations = $gdm->get_registered_types();
		foreach($registrations as $registration)
		{
			$uvrlo = new UserViewRelLearningObject();
			$uvrlo->set_view_id($this->get_id());
			$uvrlo->set_learning_object_type($registration);
			$uvrlo->set_visibility(1);
			$uvrlo->create();
		}
		
		return $success;
	}
	
	function update() 
	{
		$gdm = RepositoryDataManager :: get_instance();
		$success = $gdm->update_user_view($this);
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