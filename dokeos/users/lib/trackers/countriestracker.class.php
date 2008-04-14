<?php

/**
 * @package users.lib.trackers
 */
 
require_once dirname(__FILE__) . '/usertracker.class.php';
 
/**
 * This class tracks the country that a user uses
 */
class CountriesTracker extends UserTracker
{
	/**
	 * Constructor sets the default values
	 */
    function CountriesTracker() 
    {
    	parent :: UserTracker();
    	$this->set_property(self :: PROPERTY_TYPE, 'country');
    }
    
    function track($parameters = array())
    {
    	
    }
}
?>