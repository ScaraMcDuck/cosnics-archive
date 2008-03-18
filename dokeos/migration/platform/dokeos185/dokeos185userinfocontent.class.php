<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 userinfo_content
 *
 * @author Sven Vanpoucke
 */
class Dokeos185UserinfoContent
{
	/**
	 * Dokeos185UserinfoContent properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_DEFINITION_ID = 'definition_id';
	const PROPERTY_EDITOR_IP = 'editor_ip';
	const PROPERTY_EDITION_TIME = 'edition_time';
	const PROPERTY_CONTENT = 'content';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185UserinfoContent object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185UserinfoContent($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_USER_ID, SELF :: PROPERTY_DEFINITION_ID, SELF :: PROPERTY_EDITOR_IP, SELF :: PROPERTY_EDITION_TIME, SELF :: PROPERTY_CONTENT);
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
	 * Returns the id of this Dokeos185UserinfoContent.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the user_id of this Dokeos185UserinfoContent.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Returns the definition_id of this Dokeos185UserinfoContent.
	 * @return the definition_id.
	 */
	function get_definition_id()
	{
		return $this->get_default_property(self :: PROPERTY_DEFINITION_ID);
	}

	/**
	 * Returns the editor_ip of this Dokeos185UserinfoContent.
	 * @return the editor_ip.
	 */
	function get_editor_ip()
	{
		return $this->get_default_property(self :: PROPERTY_EDITOR_IP);
	}

	/**
	 * Returns the edition_time of this Dokeos185UserinfoContent.
	 * @return the edition_time.
	 */
	function get_edition_time()
	{
		return $this->get_default_property(self :: PROPERTY_EDITION_TIME);
	}

	/**
	 * Returns the content of this Dokeos185UserinfoContent.
	 * @return the content.
	 */
	function get_content()
	{
		return $this->get_default_property(self :: PROPERTY_CONTENT);
	}


}

?>