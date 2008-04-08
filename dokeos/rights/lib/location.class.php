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
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';
	
	/**#@-*/

	/**
	 * Numeric identifier of the user object.
	 */
	private $id;

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
	function Location($id = 0, $defaultProperties = array ())
	{
		$this->id = $id;
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION);
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
		return $this->id;
	}
	
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}
		
	function set_id($id)
	{
		$this->id = $id;
	}	
	
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
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