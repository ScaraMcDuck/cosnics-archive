<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 tool_intro
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ToolIntro
{
	/**
	 * Dokeos185ToolIntro properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_INTRO_TEXT = 'intro_text';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185ToolIntro object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185ToolIntro($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_INTRO_TEXT);
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
	 * Returns the id of this Dokeos185ToolIntro.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185ToolIntro.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the intro_text of this Dokeos185ToolIntro.
	 * @return the intro_text.
	 */
	function get_intro_text()
	{
		return $this->get_default_property(self :: PROPERTY_INTRO_TEXT);
	}

	/**
	 * Sets the intro_text of this Dokeos185ToolIntro.
	 * @param intro_text
	 */
	function set_intro_text($intro_text)
	{
		$this->set_default_property(self :: PROPERTY_INTRO_TEXT, $intro_text);
	}

}

?>