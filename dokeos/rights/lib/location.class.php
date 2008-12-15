<?php
/**
 * @package users
 */
/**
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */

class Location
{
	const PROPERTY_ID = 'id';
	const PROPERTY_LOCATION = 'location';
	const PROPERTY_LEFT_VALUE = 'left_value';
	const PROPERTY_RIGHT_VALUE = 'right_value';
	const PROPERTY_PARENT = 'parent';
	const PROPERTY_APPLICATION  = 'application';
	const PROPERTY_TYPE  = 'type';
	const PROPERTY_IDENTIFIER  = 'identifier';
	const PROPERTY_INHERIT  = 'inherit';
	const PROPERTY_LOCKED  = 'locked';
	
	/**#@-*/

	/**
	 * Default properties of the user object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	function update() 
	{
		$rdm = RightsDataManager :: get_instance();
		$success = $rdm->update_location($this);
		if (!$success)
		{
			return false;
		}

		return true;	
	}

	/**
	 * Creates a new user object.
	 * @param int $id The numeric ID of the user object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the user
	 *                                 object. Associative array.
	 */
	function Location($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this user object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this user.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all users.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_LOCATION, self :: PROPERTY_LEFT_VALUE, self :: PROPERTY_RIGHT_VALUE, self :: PROPERTY_PARENT, self :: PROPERTY_APPLICATION, self :: PROPERTY_TYPE, self :: PROPERTY_IDENTIFIER, self :: PROPERTY_INHERIT, self :: PROPERTY_LOCKED);
	}
		
	/**
	 * Sets a default property of this user by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default user
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
		
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	function get_location()
	{
		return $this->get_default_property(self :: PROPERTY_LOCATION);
	}
		
	function set_location($location)
	{
		$this->set_default_property(self :: PROPERTY_LOCATION, $location);
	}
	
	function get_left_value()
	{
		return $this->get_default_property(self :: PROPERTY_LEFT_VALUE);
	}
		
	function set_left_value($left_value)
	{
		$this->set_default_property(self :: PROPERTY_LEFT_VALUE, $left_value);
	}
	
	function get_right_value()
	{
		return $this->get_default_property(self :: PROPERTY_RIGHT_VALUE);
	}
		
	function set_right_value($right_value)
	{
		$this->set_default_property(self :: PROPERTY_RIGHT_VALUE, $right_value);
	}
	
	function get_parent()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT);
	}
		
	function set_parent($parent)
	{
		$this->set_default_property(self :: PROPERTY_PARENT, $parent);
	}
	
	function get_application()
	{
		return $this->get_default_property(self :: PROPERTY_APPLICATION);
	}
		
	function set_application($application)
	{
		$this->set_default_property(self :: PROPERTY_APPLICATION, $application);
	}
	
	function get_type()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE);
	}
		
	function set_type($type)
	{
		$this->set_default_property(self :: PROPERTY_TYPE, $type);
	}
	
	function get_identifier()
	{
		return $this->get_default_property(self :: PROPERTY_IDENTIFIER);
	}
		
	function set_identifier($identifier)
	{
		$this->set_default_property(self :: PROPERTY_IDENTIFIER, $identifier);
	}
	
	function get_inherit()
	{
		return $this->get_default_property(self :: PROPERTY_INHERIT);
	}
		
	function set_inherit($inherit)
	{
		$this->set_default_property(self :: PROPERTY_INHERIT, $inherit);
	}
	
	function inherits()
	{
		return $this->get_inherit();
	}
	
	function get_locked()
	{
		return $this->get_default_property(self :: PROPERTY_LOCKED);
	}
		
	function set_locked($locked)
	{
		$this->set_default_property(self :: PROPERTY_LOCKED, $locked);
	}
	
	function is_locked()
	{
		return $this->get_locked();
	}
	
	function lock()
	{
		$this->set_locked(true);
	}
	
	function unlock()
	{
		$this->set_locked(false);
	}
	
	function is_root()
	{
		$parent = $this->get_parent();
		return ($parent == 0);
	}
	
	/**
	 * Instructs the Datamanager to delete this user.
	 * @return boolean True if success, false otherwise.
	 */
	function delete()
	{
		return RightsDataManager :: get_instance()->delete_location($this);
	}
	
	function create()
	{
		$rdm = RightsDataManager :: get_instance();
		$this->set_id($rdm->get_next_location_id());
		return $rdm->create_location($this);
	}
}
?>