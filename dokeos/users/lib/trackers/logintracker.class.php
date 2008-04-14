<?php

/**
 * @package users.lib.trackers
 */
 
require_once Path :: get_tracking_path() . 'lib/maintracker.class.php';
 
/**
 * This class tracks the login that a user uses
 */
class LoginTracker extends MainTracker
{
	const PROPERTY_ID = 'id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_DATE = 'date';
	const PROPERTY_IP = 'ip';
	
	/**
	 * Constructor sets the default values
	 */
    function LoginTracker() 
    {
    	parent :: MainTracker('login');
    }
    
    function track($parameters = array())
    {
    	
    }
    
    /**
     * Get's the id of the login tracker
     * @return int $id the id
     */
    function get_id()
    {
    	return $this->get_property(self :: PROPERTY_ID);
    }
    
    /**
     * Sets the id of the login tracker
     * @param int $id the id
     */
    function set_id($id)
    {
    	$this->set_property(self :: PROPERTY_ID, $id);
    }
    
    /**
     * Get's the userid of the login tracker
     * @return int $userid the userid
     */
    function get_userid()
    {
    	return $this->get_property(self :: PROPERTY_USER_ID);
    }
    
    /**
     * Sets the userid of the login tracker
     * @param int $userid the userid
     */
    function set_userid($userid)
    {
    	$this->set_property(self :: PROPERTY_USER_ID, $userid);
    }
    
    /**
     * Get's the name of the login tracker
     * @return int $name the name
     */
    function get_name()
    {
    	return $this->get_property(self :: PROPERTY_DATE);
    }
    
    /**
     * Sets the name of the login tracker
     * @param int $name the name
     */
    function set_name($name)
    {
    	$this->set_property(self :: PROPERTY_DATE, $date);
    }
    
    /**
     * Get's the ip of the login tracker
     * @return int $ip the ip
     */
    function get_ip()
    {
    	return $this->get_property(self :: PROPERTY_IP);
    }
    
    /**
     * Sets the ip of the login tracker
     * @param int $ip the ip
     */
    function set_ip($ip)
    {
    	$this->set_property(self :: PROPERTY_IP, $ip);
    }

}
?>