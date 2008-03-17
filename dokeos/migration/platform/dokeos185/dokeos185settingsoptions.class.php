<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 settings_options
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SettingsOptions
{
	/**
	 * Dokeos185SettingsOptions properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_VARIABLE = 'variable';
	const PROPERTY_VALUE = 'value';
	const PROPERTY_DISPLAY_TEXT = 'display_text';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185SettingsOptions object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185SettingsOptions($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_VARIABLE, SELF :: PROPERTY_VALUE, SELF :: PROPERTY_DISPLAY_TEXT);
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
	 * Returns the id of this Dokeos185SettingsOptions.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185SettingsOptions.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the variable of this Dokeos185SettingsOptions.
	 * @return the variable.
	 */
	function get_variable()
	{
		return $this->get_default_property(self :: PROPERTY_VARIABLE);
	}

	/**
	 * Sets the variable of this Dokeos185SettingsOptions.
	 * @param variable
	 */
	function set_variable($variable)
	{
		$this->set_default_property(self :: PROPERTY_VARIABLE, $variable);
	}
	/**
	 * Returns the value of this Dokeos185SettingsOptions.
	 * @return the value.
	 */
	function get_value()
	{
		return $this->get_default_property(self :: PROPERTY_VALUE);
	}

	/**
	 * Sets the value of this Dokeos185SettingsOptions.
	 * @param value
	 */
	function set_value($value)
	{
		$this->set_default_property(self :: PROPERTY_VALUE, $value);
	}
	/**
	 * Returns the display_text of this Dokeos185SettingsOptions.
	 * @return the display_text.
	 */
	function get_display_text()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY_TEXT);
	}

	/**
	 * Sets the display_text of this Dokeos185SettingsOptions.
	 * @param display_text
	 */
	function set_display_text($display_text)
	{
		$this->set_default_property(self :: PROPERTY_DISPLAY_TEXT, $display_text);
	}

}

?>