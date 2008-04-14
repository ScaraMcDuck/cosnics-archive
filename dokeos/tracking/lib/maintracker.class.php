<?php
/**
 * @package tracking.lib
 */

/**
 * This class defines the main tracker class, every tracker must extend this class in order to work
 * @author Sven Vanpoucke
 */
abstract class MainTracker
{
	/**
	 * The table where the tracker should write to
	 */
	private $table; 
	
	/**
	 * The properties of the tracker
	 */
	private $properties = array();
	
	/**
	 * Constructor
	 * @param String $table the tablename the tracker should write to
	 */
	function MainTracker($table)
	{
		$this->table = table;
	}
	
	/**
	 * Write the values of the properties from the tracker to the database
	 * @return true if creation is succesful
	 */
	function create()
	{
		
	}
	
	/**
	 * Update the values of the properties from the tracker to the database
	 */
	function update()
	{
		
	}
	
	/**
	 * Returns the table of the tracker
	 * @return string the tablename
	 */
	function get_table()
	{
		return $this->table();
	}
	
	/**
	 * Sets the tablename of the tracker
	 * @param String $table the tablename
	 */
	function set_table($table)
	{
		$this->table = $table;
	}

	/**
	 * Returns the value of the property with the given name
	 * @param string $name The propertyname
	 * @return string the value
	 */
	function get_property($name)
	{
		return $this->properties[$name];
	}
	
	/**
	 * Set the value of a property
	 * @param string $name the property name
	 * @param string $value the property value
	 */
	function set_property($name, $value)
	{
		$this->properties[$name] = $value;
	}
	
	/**
	 * Returns all properties
	 * @return array of properties
	 */
	function get_properties()
	{
		return $this->properties;
	}
	
	/**
	 * Set the properties of the tracker
	 * @param array $properties
	 */
	function set_properties($properties)
	{
		$this->properties = $properties;
	}
	
	/**
	 * Returns the property names of the tracker
	 */
	abstract function get_property_names();
	/**
	 * Method to start the actual tracking
	 * @param array $parameters
	 */
	abstract function track($parameters = array());
}

?>
