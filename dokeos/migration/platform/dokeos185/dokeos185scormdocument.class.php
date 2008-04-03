<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 scormdocument
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Scormdocument
{
	/**
	 * Dokeos185Scormdocument properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_PATH = 'path';
	const PROPERTY_VISIBILITY = 'visibility';
	const PROPERTY_COMMENT = 'comment';
	const PROPERTY_FILETYPE = 'filetype';
	const PROPERTY_NAME = 'name';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185Scormdocument object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185Scormdocument($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_PATH, self :: PROPERTY_VISIBILITY, self :: PROPERTY_COMMENT, self :: PROPERTY_FILETYPE, self :: PROPERTY_NAME);
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
	 * Returns the id of this Dokeos185Scormdocument.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the path of this Dokeos185Scormdocument.
	 * @return the path.
	 */
	function get_path()
	{
		return $this->get_default_property(self :: PROPERTY_PATH);
	}

	/**
	 * Returns the visibility of this Dokeos185Scormdocument.
	 * @return the visibility.
	 */
	function get_visibility()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBILITY);
	}

	/**
	 * Returns the comment of this Dokeos185Scormdocument.
	 * @return the comment.
	 */
	function get_comment()
	{
		return $this->get_default_property(self :: PROPERTY_COMMENT);
	}

	/**
	 * Returns the filetype of this Dokeos185Scormdocument.
	 * @return the filetype.
	 */
	function get_filetype()
	{
		return $this->get_default_property(self :: PROPERTY_FILETYPE);
	}

	/**
	 * Returns the name of this Dokeos185Scormdocument.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}


}

?>