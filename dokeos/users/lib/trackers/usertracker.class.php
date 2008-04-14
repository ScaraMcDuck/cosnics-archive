<?php

/**
 * @package users.lib.trackers
 */
 
require_once Path :: get_tracking_path() . 'lib/maintracker.class.php';
 
/**
 * This class is a abstract class for user tracking
 */
abstract class UserTracker extends MainTracker
{
	const PROPERTY_ID = 'id';
	const PROPERTY_TYPE = 'type';
	const PROPERTY_NAME = 'name';
	const PROPERTY_VALUE = 'value';
	
	/**
	 * Constructor sets the default values
	 */
    function UserTracker() 
    {
    	parent :: MainTracker('user');
    }
    
    /**
     * Inherited
     */
    abstract function track($parameters = array());
    
    /**
     * Inherited
     */
    function get_property_names()
    {
    	return array(self :: PROPERTY_ID, self :: PROPERTY_TYPE, 
    				 self :: PROPERTY_NAME, self :: PROPERTY_VALUE);
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
     * Get's the type of the user tracker
     * @return int $type the type
     */
    function get_type()
    {
    	return $this->get_property(self :: PROPERTY_TYPE);
    }
    
    /**
     * Sets the type of the user tracker
     * @param int $type the type
     */
    function set_type($type)
    {
    	$this->set_property(self :: PROPERTY_TYPE, $type);
    }
    
    /**
     * Get's the name of the user tracker
     * @return int $name the name
     */
    function get_name()
    {
    	return $this->get_property(self :: PROPERTY_NAME);
    }
    
    /**
     * Sets the name of the user tracker
     * @param int $name the name
     */
    function set_name($name)
    {
    	$this->set_property(self :: PROPERTY_NAME, $name);
    }
    
    /**
     * Get's the value of the user tracker
     * @return int $value the value
     */
    function get_value()
    {
    	return $this->get_property(self :: PROPERTY_VALUE);
    }
    
    /**
     * Sets the value of the user tracker
     * @param int $value the value
     */
    function set_value($value)
    {
    	$this->set_property(self :: PROPERTY_VALUE, $value);
    }

}
?>