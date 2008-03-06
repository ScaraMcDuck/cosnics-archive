<?php
/**
 * Class that represents a FailedElement
 * 
 * @author Sven Vanpoucke
 */
class FailedElement 
{
	const PROPERTY_ID = 'id';
	const PROPERTY_FAILED_ID = 'failed_id';
	const PROPERTY_TABLE_NAME = 'table_name';
	
	/**
	 * Default properties stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new failed element object
	 * @param array $defaultProperties The default properties as associative array.
	 */
	function FailedElement($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property
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
		return array (self::PROPERTY_ID, self :: PROPERTY_FAILED_ID, self :: PROPERTY_TABLE_NAME);
	}
	
	/**
	 * Sets the value of a default property
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
	 * Returns the id of this failed element
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	 
	/**
	 * Returns the id of the failed element
	 * @return int the id of the failed element
	 */
	function get_failed_id()
	{
		return $this->get_default_property(self :: PROPERTY_FAILED_ID);
	}
	
	/**
	 * Returns the table name of the failed element
	 * @return int the table name of the failed element
	 */
	function get_table_name()
	{
		return $this->get_default_property(self :: PROPERTY_TABLE_NAME);
	}
	
	/**
	 * Sets the id of this failed element.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the failed id of this failed element.
	 * @param int $id The failed id in the old table
	 */
	function set_failed_id($failed_id)
	{
		$this->set_default_property(self :: PROPERTY_FAILED_ID, $failed_id);
	}
	
	/**
	 * Sets the table name of this failed element
	 * @param string $table_name The table name
	 */
	function set_table_name($table_name)
	{
		$this->set_default_property(self :: PROPERTY_TABLE_NAME, $table_name);
	}
	
	function create()
	{
		MigrationDataManager :: getInstance('dokeos185', '')->create_failed_element($this);
	}
}
?>