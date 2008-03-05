<?php
/**
 * @package users
 */
/**
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */

class RoleRightLocation
{
	const PROPERTY_RIGHT_ID = 'right_id';
	const PROPERTY_LOCATION_ID = 'location_id';
	const PROPERTY_ROLE_ID = 'role_id';
	const PROPERTY_VALUE = 'value';

	private $right_id;
	private $role_id;
	private $location_id;
	
	private $defaultProperties;

	function update() 
	{
		$rdm = RightsDataManager :: get_instance();
		$success = $rdm->update_rolerightlocation($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
	
	function RoleRightLocation($right_id = 0, $role_id = 0, $location_id = 0, $defaultProperties = array ())
	{
		$this->right_id = $right_id;
		$this->role_id = $role_id;
		$this->location_id = $location_id;
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
		return array (self :: PROPERTY_RIGHT_ID, self :: PROPERTY_ROLE_ID, self :: PROPERTY_LOCATION_ID, self :: PROPERTY_VALUE);
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
	
	function get_right_id()
	{
		return $this->right_id;
	}
	
	function set_right_id($right_id)
	{
		$this->right_id = $right_id;
	}
	
	function get_role_id()
	{
		return $this->role_id;
	}
	
	function set_role_id($role_id)
	{
		$this->role_id = $role_id;
	}	
	
	function get_location_id()
	{
		return $this->location_id;
	}
	
	function set_location_id($location_id)
	{
		$this->location_id = $location_id;
	}
	
	function get_value()
	{
		return $this->get_default_property(self :: PROPERTY_VALUE);
	}
	
	function set_value($value)
	{
		$this->set_default_property(self :: PROPERTY_VALUE, $value);
	}
	
	/**
	 * Instructs the Datamanager to delete this user.
	 * @return boolean True if success, false otherwise.
	 */
	function delete()
	{
		return UserDataManager :: get_instance()->delete_rolerightlocation($this);
	}
	
	function create()
	{
		$rdm = RightsDataManager :: get_instance();
		return $rdm->create_rolerightlocation($this);
	}
}
?>