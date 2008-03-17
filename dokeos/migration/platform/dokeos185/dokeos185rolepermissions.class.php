<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 role_permissions
 *
 * @author Sven Vanpoucke
 */
class Dokeos185RolePermissions
{
	/**
	 * Dokeos185RolePermissions properties
	 */
	const PROPERTY_ROLE_ID = 'role_id';
	const PROPERTY_TOOL = 'tool';
	const PROPERTY_ACTION = 'action';
	const PROPERTY_DEFAULT_PERM = 'default_perm';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185RolePermissions object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185RolePermissions($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ROLE_ID, SELF :: PROPERTY_TOOL, SELF :: PROPERTY_ACTION, SELF :: PROPERTY_DEFAULT_PERM);
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
	 * Returns the role_id of this Dokeos185RolePermissions.
	 * @return the role_id.
	 */
	function get_role_id()
	{
		return $this->get_default_property(self :: PROPERTY_ROLE_ID);
	}

	/**
	 * Sets the role_id of this Dokeos185RolePermissions.
	 * @param role_id
	 */
	function set_role_id($role_id)
	{
		$this->set_default_property(self :: PROPERTY_ROLE_ID, $role_id);
	}
	/**
	 * Returns the tool of this Dokeos185RolePermissions.
	 * @return the tool.
	 */
	function get_tool()
	{
		return $this->get_default_property(self :: PROPERTY_TOOL);
	}

	/**
	 * Sets the tool of this Dokeos185RolePermissions.
	 * @param tool
	 */
	function set_tool($tool)
	{
		$this->set_default_property(self :: PROPERTY_TOOL, $tool);
	}
	/**
	 * Returns the action of this Dokeos185RolePermissions.
	 * @return the action.
	 */
	function get_action()
	{
		return $this->get_default_property(self :: PROPERTY_ACTION);
	}

	/**
	 * Sets the action of this Dokeos185RolePermissions.
	 * @param action
	 */
	function set_action($action)
	{
		$this->set_default_property(self :: PROPERTY_ACTION, $action);
	}
	/**
	 * Returns the default_perm of this Dokeos185RolePermissions.
	 * @return the default_perm.
	 */
	function get_default_perm()
	{
		return $this->get_default_property(self :: PROPERTY_DEFAULT_PERM);
	}

	/**
	 * Sets the default_perm of this Dokeos185RolePermissions.
	 * @param default_perm
	 */
	function set_default_perm($default_perm)
	{
		$this->set_default_property(self :: PROPERTY_DEFAULT_PERM, $default_perm);
	}

}

?>