<?php
/**
 * Class that represents a RecoveryElement
 * 
 * @author Sven Vanpoucke
 */
class RecoveryElement
{
	const PROPERTY_ID = 'id';
	const PROPERTY_OLD_PATH = 'old_path';
	const PROPERTY_NEW_PATH = 'new_path';
	
	/**
	 * Default properties stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new recovery element object
	 * @param array $defaultProperties The default properties as associative array.
	 */
	function RecoveryElement($defaultProperties = array ())
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
		return array (self::PROPERTY_ID, self :: PROPERTY_OLD_PATH, 
			self :: PROPERTY_NEW_PATH);
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
	 * Returns the id of this recovery element
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_CLASS_ID);
	}
	 
	/**
	 * Returns the old path of the recovery element
	 * @return int the old path of the recovery element
	 */
	function get_old_path()
	{
		return $this->get_default_property(self :: PROPERTY_OLD_PATH);
	}
	
	/**
	 * Returns the new path of the recovery element
	 * @return int the new path of the recovery element
	 */
	function get_new_path()
	{
		return $this->get_default_property(self :: PROPERTY_NEW_PATH);
	}
	
	/**
	 * Sets the id of this recovery element.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the old path of this recovery element.
	 * @param int $id The old path in the old table
	 */
	function set_old_path($old_path)
	{
		$this->set_default_property(self :: PROPERTY_OLD_PATH, $old_path);
	}
	
	/**
	 * Sets the new path of this recovery element.
	 * @param int $id The new path in the new table
	 */
	function set_new_path($new_path)
	{
		$this->set_default_property(self :: PROPERTY_NEW_PATH, $new_path);
	}
	
	function create()
	{
		MigrationDataManager :: getInstance('dokeos185', '')->create_recovery_element($this);
	}
}
?>