<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 permission_task
 *
 * @author Sven Vanpoucke
 */
class Dokeos185PermissionTask
{
	/**
	 * Dokeos185PermissionTask properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_TASK_ID = 'task_id';
	const PROPERTY_TOOL = 'tool';
	const PROPERTY_ACTION = 'action';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185PermissionTask object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185PermissionTask($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_TASK_ID, SELF :: PROPERTY_TOOL, SELF :: PROPERTY_ACTION);
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
	 * Returns the id of this Dokeos185PermissionTask.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185PermissionTask.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the task_id of this Dokeos185PermissionTask.
	 * @return the task_id.
	 */
	function get_task_id()
	{
		return $this->get_default_property(self :: PROPERTY_TASK_ID);
	}

	/**
	 * Sets the task_id of this Dokeos185PermissionTask.
	 * @param task_id
	 */
	function set_task_id($task_id)
	{
		$this->set_default_property(self :: PROPERTY_TASK_ID, $task_id);
	}
	/**
	 * Returns the tool of this Dokeos185PermissionTask.
	 * @return the tool.
	 */
	function get_tool()
	{
		return $this->get_default_property(self :: PROPERTY_TOOL);
	}

	/**
	 * Sets the tool of this Dokeos185PermissionTask.
	 * @param tool
	 */
	function set_tool($tool)
	{
		$this->set_default_property(self :: PROPERTY_TOOL, $tool);
	}
	/**
	 * Returns the action of this Dokeos185PermissionTask.
	 * @return the action.
	 */
	function get_action()
	{
		return $this->get_default_property(self :: PROPERTY_ACTION);
	}

	/**
	 * Sets the action of this Dokeos185PermissionTask.
	 * @param action
	 */
	function set_action($action)
	{
		$this->set_default_property(self :: PROPERTY_ACTION, $action);
	}

}

?>