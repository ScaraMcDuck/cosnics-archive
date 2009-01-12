<?php

require_once dirname(__FILE__).'/help_data_manager.class.php';

class HelpItem
{
	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_NAME = 'name';
	const PROPERTY_URL = 'url';
	
	/**
	 * Default properties of the group object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new group object.
	 * @param int $id The numeric ID of the group object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the group
	 *                                 object. Associative array.
	 */
	function Group($id = 0, $defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this group object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this group.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Get the default properties of all groups.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_NAME, self :: PROPERTY_URL);
	}
		
	/**
	 * Sets a default property of this group by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default group
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
	 * Returns the name of this group.
	 * @return String The name
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	/**
	 * Returns the url of this group.
	 * @return String The url
	 */
	function get_url()
	{
		return $this->get_default_property(self :: PROPERTY_URL);
	}
	
	/**
	 * Sets the name of this group.
	 * @param String $name the name.
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	/**
	 * Sets the url of this group.
	 * @param String $url the url.
	 */
	function set_url($url)
	{
		$this->set_default_property(self :: PROPERTY_URL, $url);
	}
	
	function create()
	{
		$hdm = HelpDataManager :: get_instance();
		return $hdm->create_help_item($this);
	}
	
	function update() 
	{
		$hdm = HelpDataManager :: get_instance();
		return 	$hdm->update_help_item($this);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>