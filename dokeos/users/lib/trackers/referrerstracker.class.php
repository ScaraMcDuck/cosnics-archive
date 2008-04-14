<?php

/**
 * @package users.lib.trackers
 */
 
require_once dirname(__FILE__) . '/usertracker.class.php';
 
/**
 * This class tracks the referer that a user uses
 */
class ReferersTracker extends UserTracker
{
	/**
	 * Constructor sets the default values
	 */
    function ReferersTracker() 
    {
    	parent :: UserTracker();
    	$this->set_property(self :: PROPERTY_TYPE, 'referer');
    }
    
    function track($parameters = array())
    {
    	
    }
}
?>