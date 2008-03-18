<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 dropbox_person
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxPerson
{
	/**
	 * Dokeos185DropboxPerson properties
	 */
	const PROPERTY_FILE_ID = 'file_id';
	const PROPERTY_USER_ID = 'user_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185DropboxPerson object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185DropboxPerson($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_FILE_ID, SELF :: PROPERTY_USER_ID);
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
	 * Returns the file_id of this Dokeos185DropboxPerson.
	 * @return the file_id.
	 */
	function get_file_id()
	{
		return $this->get_default_property(self :: PROPERTY_FILE_ID);
	}

	/**
	 * Returns the user_id of this Dokeos185DropboxPerson.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}


}

?>