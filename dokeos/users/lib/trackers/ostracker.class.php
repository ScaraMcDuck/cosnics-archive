<?php

/**
 * @package users.lib.trackers
 */
 
require_once dirname(__FILE__) . '/usertracker.class.php';
 
/**
 * This class tracks the os that a user uses
 */
class OSTracker extends UserTracker
{
	/**
	 * Constructor sets the default values
	 */
    function OSTracker() 
    {
    	parent :: UserTracker();
    	$this->set_property(self :: PROPERTY_TYPE, 'os');
    }
    
    function track($parameters = array())
    {
    	
    }
}
?>