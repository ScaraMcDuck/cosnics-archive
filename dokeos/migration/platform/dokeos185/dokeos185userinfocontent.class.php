<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 userinfo_content
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
	 * Sets the id of this Dokeos185UserinfoContent.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
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
	 * Sets the user_id of this Dokeos185UserinfoContent.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
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
	 * Sets the definition_id of this Dokeos185UserinfoContent.
	 * @param definition_id
	 */
	function set_definition_id($definition_id)
	{
		$this->set_default_property(self :: PROPERTY_DEFINITION_ID, $definition_id);
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
	 * Sets the editor_ip of this Dokeos185UserinfoContent.
	 * @param editor_ip
	 */
	function set_editor_ip($editor_ip)
	{
		$this->set_default_property(self :: PROPERTY_EDITOR_IP, $editor_ip);
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
	 * Sets the edition_time of this Dokeos185UserinfoContent.
	 * @param edition_time
	 */
	function set_edition_time($edition_time)
	{
		$this->set_default_property(self :: PROPERTY_EDITION_TIME, $edition_time);
	}
	/**
	 * Returns the content of this Dokeos185UserinfoContent.
	 * @return the content.
	 */
	function get_content()
	{
		return $this->get_default_property(self :: PROPERTY_CONTENT);
	}

	/**
	 * Sets the content of this Dokeos185UserinfoContent.
	 * @param content
	 */
	function set_content($content)
	{
		$this->set_default_property(self :: PROPERTY_CONTENT, $content);
	}

}

?>