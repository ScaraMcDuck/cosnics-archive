<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 permission_group
 *
 * @author Sven Vanpoucke
 */
class Dokeos185PermissionGroup
{
	/**
	 * Dokeos185PermissionGroup properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_GROUP_ID = 'group_id';
	const PROPERTY_TOOL = 'tool';
	const PROPERTY_ACTION = 'action';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185PermissionGroup object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185PermissionGroup($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_GROUP_ID, SELF :: PROPERTY_TOOL, SELF :: PROPERTY_ACTION);
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
	 * Returns the id of this Dokeos185PermissionGroup.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185PermissionGroup.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the group_id of this Dokeos185PermissionGroup.
	 * @return the group_id.
	 */
	function get_group_id()
	{
		return $this->get_default_property(self :: PROPERTY_GROUP_ID);
	}

	/**
	 * Sets the group_id of this Dokeos185PermissionGroup.
	 * @param group_id
	 */
	function set_group_id($group_id)
	{
		$this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
	}
	/**
	 * Returns the tool of this Dokeos185PermissionGroup.
	 * @return the tool.
	 */
	function get_tool()
	{
		return $this->get_default_property(self :: PROPERTY_TOOL);
	}

	/**
	 * Sets the tool of this Dokeos185PermissionGroup.
	 * @param tool
	 */
	function set_tool($tool)
	{
		$this->set_default_property(self :: PROPERTY_TOOL, $tool);
	}
	/**
	 * Returns the action of this Dokeos185PermissionGroup.
	 * @return the action.
	 */
	function get_action()
	{
		return $this->get_default_property(self :: PROPERTY_ACTION);
	}

	/**
	 * Sets the action of this Dokeos185PermissionGroup.
	 * @param action
	 */
	function set_action($action)
	{
		$this->set_default_property(self :: PROPERTY_ACTION, $action);
	}

}

?>