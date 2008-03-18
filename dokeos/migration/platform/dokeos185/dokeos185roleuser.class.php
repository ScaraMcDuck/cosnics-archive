<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 role_user
 *
 * @author Sven Vanpoucke
 */
class Dokeos185RoleUser
{
	/**
	 * Dokeos185RoleUser properties
	 */
	const PROPERTY_ROLE_ID = 'role_id';
	const PROPERTY_SCOPE = 'scope';
	const PROPERTY_USER_ID = 'user_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185RoleUser object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185RoleUser($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (SELF :: PROPERTY_ROLE_ID, SELF :: PROPERTY_SCOPE, SELF :: PROPERTY_USER_ID);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Returns the role_id of this Dokeos185RoleUser.
	 * @return the role_id.
	 */
	function get_role_id()
	{
		return $this->get_default_property(self :: PROPERTY_ROLE_ID);
	}

	/**
	 * Returns the scope of this Dokeos185RoleUser.
	 * @return the scope.
	 */
	function get_scope()
	{
		return $this->get_default_property(self :: PROPERTY_SCOPE);
	}

	/**
	 * Returns the user_id of this Dokeos185RoleUser.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}


}

?>