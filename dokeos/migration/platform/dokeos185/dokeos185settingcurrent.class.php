<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importsetting.class.php';

/**
 * This class represents an old Dokeos 1.8.5 class
 *
 * @author David Van Wayenberghµ
 * @author Sven Vanpoucke
 */

class Dokeos185SettingCurrent extends Import
{
	/**
	 * Migration data manager
	 */
	private static $mgdm;

	/**
	 * current setting properties
	 */
	 
	const PROPERTY_ID = 'id';
	const PROPERTY_VARIABLE = 'variable';
	const PROPERTY_CATEGORY = 'category';
	const PROPERTY_SELECTED_VALUE = 'selected_value';
	
	
	/**
	 * Alfanumeric identifier of the current setting object.
	 */
	private $code;
	
	/**
	 * Default properties of the current setting object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new current setting object.
	 * @param array $defaultProperties The default properties of the current setting
	 *                                 object. Associative array.
	 */
	function Dokeos185SettingCurrent($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this current setting object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this current setting.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all current setting.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self::PROPERTY_ID, self :: PROPERTY_CODE, 
		self::PROPERTY_NAME);
	}
	
	/**
	 * Sets a default property of this current setting by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default current setting
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	/**
	 * Returns the id of this current setting.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	 
	/**
	 * Returns the variable of this current setting.
	 * @return String The code.
	 */
	function get_code()
	{
		return $this->get_default_property(self :: PROPERTY_CODE);
	}
	
	/**
	 * Returns the name of this current setting.
	 * @return int The name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_Name);
	}
}
?>
