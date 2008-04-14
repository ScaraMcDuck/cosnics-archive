<?php

/**
 * @package users.lib.trackers
 */
 
require_once dirname(__FILE__) . '/usertracker.class.php';
 
/**
 * This class tracks the provider that a user uses
 */
class ProvidersTracker extends UserTracker
{
	/**
	 * Constructor sets the default values
	 */
    function ProvidersTracker() 
    {
    	parent :: UserTracker();
    	$this->set_property(self :: PROPERTY_TYPE, 'provider');
    }
    
    function track($parameters = array())
    {
    	
    }
}
?>