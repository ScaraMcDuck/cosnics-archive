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
	const PROPERTY_ID = 'id';

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
		$this->table = $table;
	}
	
	/**
	 * Write the values of the properties from the tracker to the database
	 * @return true if creation is succesful
	 */
	function create()
	{
		$trkdmg = TrackingDataManager :: get_instance();
		$this->set_id($trkdmg->get_next_id($this->table));
		return $trkdmg->create_tracker_item($this->table, $this);
	}
	
	/**
	 * Update the values of the properties from the tracker to the database
	 */
	function update()
	{
		$trkdmg = TrackingDataManager :: get_instance();
		return $trkdmg->update_tracker_item($this->table, $this);
	}
	
	/**
	 * Retrieves tracker items with a given condition
	 * @param Condition condition on which we want to retrieve the trackers
	 * @return array of tracker items
	 */
	function retrieve_tracker_items($condition)
	{
		$trkdmg = TrackingDataManager :: get_instance();
		return $trkdmg->retrieve_tracker_items($this->table, get_class($this), $condition);
	}
	
	/**
	 * Removes tracker items with a given condition
	 */
	function remove($condition)
	{
		$trkdmg = TrackingDataManager :: get_instance();
		return $trkdmg->remove_tracker_items($this->table, $condition);
	}
	
	/**
	 * Returns the table of the tracker
	 * @return string the tablename
	 */
	function get_table()
	{
		return $this->table;
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
	function get_default_properties()
	{
		return $this->properties;
	}
	
	/**
	 * Set the properties of the tracker
	 * @param array $properties
	 */
	function set_default_properties($properties)
	{
		$this->properties = $properties;
	}
	
	/**
	 * Returns the property names of the tracker
	 */
	function get_default_property_names()
	{  
		return array(self :: PROPERTY_ID);	
	}
	
    /**
     * Get's the id of the user tracker
     * @return int $id the id
     */
    function get_id()
    {
    	return $this->get_property(self :: PROPERTY_ID);
    }
    
    /**
     * Sets the id of the user tracker
     * @param int $id the id
     */
    function set_id($id)
    {
    	$this->set_property(self :: PROPERTY_ID, $id);
    }
	
	/**
	 * Method to start the actual tracking
	 * @param array $parameters
	 */
	abstract function track($parameters = array());
	
	/**
	 * Method to clear the tracker table for given event
	 * @param Event $event
	 * @return true if correctly cleared
	 */
	abstract function empty_tracker($event);
	
	function to_db_date($date)
	{
		$trkdmg = TrackingDataManager :: get_instance();
		return $trkdmg->to_db_date($date);
	}
}

?>
