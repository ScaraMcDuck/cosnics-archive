<?php

/**
 * @package users.lib.trackers
 */
 
require_once dirname(__FILE__) . '/usertracker.class.php';
 
/**
 * This class tracks the browser that a user uses
 */
class BrowsersTracker extends UserTracker
{
	/**
	 * Constructor sets the default values
	 */
    function BrowsersTracker() 
    {
    	parent :: UserTracker();
    	$this->set_property(self :: PROPERTY_TYPE, 'browser');
    }
    
    function track($parameters = array())
    {
    	
    }
}
?>