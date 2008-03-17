<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 session_rel_user
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SessionRelUser
{
	/**
	 * Dokeos185SessionRelUser properties
	 */
	const PROPERTY_ID_SESSION = 'id_session';
	const PROPERTY_ID_USER = 'id_user';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185SessionRelUser object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185SessionRelUser($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID_SESSION, SELF :: PROPERTY_ID_USER);
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
	 * Returns the id_session of this Dokeos185SessionRelUser.
	 * @return the id_session.
	 */
	function get_id_session()
	{
		return $this->get_default_property(self :: PROPERTY_ID_SESSION);
	}

	/**
	 * Sets the id_session of this Dokeos185SessionRelUser.
	 * @param id_session
	 */
	function set_id_session($id_session)
	{
		$this->set_default_property(self :: PROPERTY_ID_SESSION, $id_session);
	}
	/**
	 * Returns the id_user of this Dokeos185SessionRelUser.
	 * @return the id_user.
	 */
	function get_id_user()
	{
		return $this->get_default_property(self :: PROPERTY_ID_USER);
	}

	/**
	 * Sets the id_user of this Dokeos185SessionRelUser.
	 * @param id_user
	 */
	function set_id_user($id_user)
	{
		$this->set_default_property(self :: PROPERTY_ID_USER, $id_user);
	}

}

?>