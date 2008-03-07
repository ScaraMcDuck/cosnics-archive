<?php
/**
 * Class that represents a Id Reference
 * 
 * @author Sven Vanpoucke
 */
class IdReference 
{
	const PROPERTY_ID = 'id';
	const PROPERTY_OLD_ID = 'old_id';
	const PROPERTY_NEW_ID = 'new_id';
	const PROPERTY_TABLE_NAME = 'table_name';
	
	/**
	 * Default properties stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new id reference object
	 * @param array $defaultProperties The default properties as associative array.
	 */
	function IdReference($defaultProperties = array ())
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
		return array (self::PROPERTY_ID, self :: PROPERTY_OLD_ID, 
			self :: PROPERTY_NEW_ID, self :: PROPERTY_TABLE_NAME);
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
	 * Returns the id of this id reference
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_CLASS_ID);
	}
	 
	/**
	 * Returns the old id of the id reference
	 * @return int the old id of the id reference
	 */
	function get_old_id()
	{
		return $this->get_default_property(self :: PROPERTY_OLD_ID);
	}
	
	/**
	 * Returns the new id of the id reference
	 * @return int the new id of the id reference
	 */
	function get_new_id()
	{
		return $this->get_default_property(self :: PROPERTY_NEW_ID);
	}
	
	/**
	 * Returns the table name of the id reference
	 * @return int the table name of the id reference
	 */
	function get_table_name()
	{
		return $this->get_default_property(self :: PROPERTY_TABLE_NAME);
	}
	
	/**
	 * Sets the id of this id reference.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the old id of this id reference.
	 * @param int $id The old id in the old table
	 */
	function set_old_id($old_id)
	{
		$this->set_default_property(self :: PROPERTY_OLD_ID, $old_id);
	}
	
	/**
	 * Sets the new id of this id reference.
	 * @param int $id The new id in the new table
	 */
	function set_new_id($new_id)
	{
		$this->set_default_property(self :: PROPERTY_NEW_ID, $new_id);
	}
	
	/**
	 * Sets the table name of this id reference
	 * @param string $table_name The table name
	 */
	function set_table_name($table_name)
	{
		$this->set_default_property(self :: PROPERTY_TABLE_NAME, $table_name);
	}
	
	function create()
	{
		MigrationDataManager :: getInstance('dokeos185', '')->create_id_reference($this);
	}
}
?>