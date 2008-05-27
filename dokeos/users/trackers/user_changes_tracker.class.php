<?php

/**
 * @package users.lib.trackers
 */
 
require_once Path :: get_tracking_path() . 'lib/default_tracker.class.php';
 
/**
 * This class tracks the login that a user uses
 */
class UserChangesTracker extends DefaultTracker
{
	
	/**
	 * Constructor sets the default values
	 */
    function UserChangesTracker() 
    {
    	parent :: MainTracker('user_changes');
    }
    
    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
    	$target_user = $parameters['target_user_id'];
    	$action_user = $parameters['action_user_id'];
    	$action = $parameters['event'];
    	
    	$this->set_user_id($action_user);
    	$this->set_reference_id($target_user);
    	$this->set_action($action);
    	$this->set_date(time());
    	
    	$this->create();
    }
    
    /**
     * Inherited
     * @see MainTracker :: empty_tracker
     */
    function empty_tracker($event)
    {
    	$condition = new EqualityCondition('action', $event->get_name());
    	return $this->remove($condition);
    }
    
    /**
     * Inherited
     */
    function export($start_date, $end_date, $event)
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition('action', $event->get_name());
    	return parent :: export($start_date, $end_date, $conditions);
    }
    
    /**
     * Inherited
     * @see MainTracker :: is_summary_tracker
     */
    function is_summary_tracker()
    {
    	return false;
    }

}
?>