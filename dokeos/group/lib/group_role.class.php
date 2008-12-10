<?php
/**
 * @package users
 */
/**
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */

class GroupRole
{
	const CLASS_NAME					= __CLASS__;
	
	const PROPERTY_GROUP_ID = 'group_id';
	const PROPERTY_ROLE_ID = 'role_id';

	private $group_id;

	/**
	 * Default properties of the userrole object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	function update() 
	{
		$udm = GroupDataManager :: get_instance();
		$success = $udm->update_group_role($this);
		if (!$success)
		{
			return false;
		}

		return true;	
	}
	
	function GroupRole($group_id = 0, $defaultProperties = array ())
	{
		$this->group_id = $group_id;
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this user quota object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this user quota object.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all users quota objects.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_GROUP_ID, self :: PROPERTY_ROLE_ID);
	}
		
	/**
	 * Sets a default property of this user quota by name.
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

	function get_group_id()
	{
		return $this->get_default_property(self :: PROPERTY_GROUP_ID);
	}
	
	function set_group_id($group_id)
	{
		$this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
	}
	
	function get_role_id()
	{
		return $this->get_default_property(self :: PROPERTY_ROLE_ID);
	}
	
	function set_role_id($role_id)
	{
		$this->set_default_property(self :: PROPERTY_ROLE_ID, $role_id);
	}	
	
	/**
	 * Instructs the Datamanager to delete this user.
	 * @return boolean True if success, false otherwise.
	 */
	function delete()
	{
		return GroupDataManager :: get_instance()->delete_group_role($this);
	}
	
	function create()
	{
		$udm = GroupDataManager :: get_instance();
		return $udm->create_group_role($this);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
}
?>