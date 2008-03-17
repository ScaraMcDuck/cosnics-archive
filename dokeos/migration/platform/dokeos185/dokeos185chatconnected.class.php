<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 chat_connected
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ChatConnected
{
	/**
	 * Dokeos185ChatConnected properties
	 */
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_LAST_CONNECTION = 'last_connection';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185ChatConnected object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185ChatConnected($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_USER_ID, SELF :: PROPERTY_LAST_CONNECTION);
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
	 * Returns the user_id of this Dokeos185ChatConnected.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this Dokeos185ChatConnected.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	/**
	 * Returns the last_connection of this Dokeos185ChatConnected.
	 * @return the last_connection.
	 */
	function get_last_connection()
	{
		return $this->get_default_property(self :: PROPERTY_LAST_CONNECTION);
	}

	/**
	 * Sets the last_connection of this Dokeos185ChatConnected.
	 * @param last_connection
	 */
	function set_last_connection($last_connection)
	{
		$this->set_default_property(self :: PROPERTY_LAST_CONNECTION, $last_connection);
	}

}

?>