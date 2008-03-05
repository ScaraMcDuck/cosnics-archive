<?php
/**
 * @package users
 */
/**
 *	This class represents a role. 
 *
 *	User objects have a number of default properties:
 *	- user_id: the numeric ID of the user;
 *	- lastname: the lastname of the user;
 *	- firstname: the firstname of the user;
 *	- password: the password for this user;
 *	- auth_source:
 *	- email: the email address of this user;
 *	- status: the status of this user: 1 is teacher, 5 is a student;
 *	- phone: the phone number of the user;
 *	- official_code; the official code of this user;
 *	- picture_uri: the URI location of the picture of this user;
 *	- creator_id: the user_id of the user who created this user;
 *	- language: the language setting of this user;
 *	- disk quota: the disk quota for this user;
 *	- database_quota: the database quota for this user;
 *	- version_quota: the default quota for this user of no quota for a specific learning object type is set.
 *
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */

class Right
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
		$success = $rdm->update_right($this);
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
	function Right($id = 0, $defaultProperties = array ())
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
		return RightsDataManager :: get_instance()->delete_right($this);
	}
	
	function create()
	{
		$rdm = RightsDataManager :: get_instance();
		$this->set_id($rdm->get_next_right_id());
		return $rdm->create_right($this);
	}
}
?>