<?php

/**
 * @package tracking.lib
 */
 
require_once dirname(__FILE__). '/maintracker.class.php';
 
/**
 * This class is an abstract class for several action trackers 
 * Has the default properties of user_id, reference_id, action and date
 */
abstract class DefaultTracker extends MainTracker
{
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_REFERENCE_ID = 'reference_id';
	const PROPERTY_ACTION = 'action';
	const PROPERTY_DATE = 'date';
	
	/**
	 * Constructor sets the default actions
	 */
    function DefaultTracker($tablereference_id) 
    {
    	parent :: MainTracker($tablereference_id);
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
    	return array_merge(parent :: get_property_names(), array(self :: PROPERTY_USER_ID, 
    				 self :: PROPERTY_REFERENCE_ID, self :: PROPERTY_ACTION));
    }
    
    /**
     * Get's the user_id of the user tracker
     * @return int $user_id the user_id
     */
    function get_user_id()
    {
    	return $this->get_property(self :: PROPERTY_USER_ID);
    }
    
    /**
     * Sets the user_id of the user tracker
     * @param int $user_id the user_id
     */
    function set_user_id($user_id)
    {
    	$this->set_property(self :: PROPERTY_USER_ID, $user_id);
    }
    
    /**
     * Get's the reference_id of the user tracker
     * @return int $reference_id the reference_id
     */
    function get_reference_id()
    {
    	return $this->get_property(self :: PROPERTY_REFERENCE_ID);
    }
    
    /**
     * Sets the reference_id of the user tracker
     * @param int $reference_id the reference_id
     */
    function set_reference_id($reference_id)
    {
    	$this->set_property(self :: PROPERTY_REFERENCE_ID, $reference_id);
    }
    
    /**
     * Get's the action of the user tracker
     * @return int $action the action
     */
    function get_action()
    {
    	return $this->get_property(self :: PROPERTY_ACTION);
    }
    
    /**
     * Sets the action of the user tracker
     * @param int $action the action
     */
    function set_action($action)
    {
    	$this->set_property(self :: PROPERTY_ACTION, $action);
    }
    
    /**
     * Get's the date of the user tracker
     * @return int $date the date
     */
    function get_date()
    {
    	return $this->get_property(self :: PROPERTY_DATE);
    }
    
    /**
     * Sets the date of the user tracker
     * @param int $date the date
     */
    function set_date($date)
    {
    	$this->set_property(self :: PROPERTY_DATE, $date);
    }

}
?>