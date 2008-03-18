<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 track_e_lastaccess
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackELastaccess
{
	/**
	 * Dokeos185TrackELastaccess properties
	 */
	const PROPERTY_ACCESS_ID = 'access_id';
	const PROPERTY_ACCESS_USER_ID = 'access_user_id';
	const PROPERTY_ACCESS_DATE = 'access_date';
	const PROPERTY_ACCESS_COURS_CODE = 'access_cours_code';
	const PROPERTY_ACCESS_TOOL = 'access_tool';
	const PROPERTY_ACCESS_SESSION_ID = 'access_session_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackELastaccess object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackELastaccess($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ACCESS_ID, SELF :: PROPERTY_ACCESS_USER_ID, SELF :: PROPERTY_ACCESS_DATE, SELF :: PROPERTY_ACCESS_COURS_CODE, SELF :: PROPERTY_ACCESS_TOOL, SELF :: PROPERTY_ACCESS_SESSION_ID);
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
	 * Returns the access_id of this Dokeos185TrackELastaccess.
	 * @return the access_id.
	 */
	function get_access_id()
	{
		return $this->get_default_property(self :: PROPERTY_ACCESS_ID);
	}

	/**
	 * Returns the access_user_id of this Dokeos185TrackELastaccess.
	 * @return the access_user_id.
	 */
	function get_access_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_ACCESS_USER_ID);
	}

	/**
	 * Returns the access_date of this Dokeos185TrackELastaccess.
	 * @return the access_date.
	 */
	function get_access_date()
	{
		return $this->get_default_property(self :: PROPERTY_ACCESS_DATE);
	}

	/**
	 * Returns the access_cours_code of this Dokeos185TrackELastaccess.
	 * @return the access_cours_code.
	 */
	function get_access_cours_code()
	{
		return $this->get_default_property(self :: PROPERTY_ACCESS_COURS_CODE);
	}

	/**
	 * Returns the access_tool of this Dokeos185TrackELastaccess.
	 * @return the access_tool.
	 */
	function get_access_tool()
	{
		return $this->get_default_property(self :: PROPERTY_ACCESS_TOOL);
	}

	/**
	 * Returns the access_session_id of this Dokeos185TrackELastaccess.
	 * @return the access_session_id.
	 */
	function get_access_session_id()
	{
		return $this->get_default_property(self :: PROPERTY_ACCESS_SESSION_ID);
	}


}

?>