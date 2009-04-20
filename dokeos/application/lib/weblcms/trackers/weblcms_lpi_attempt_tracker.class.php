<?php
require_once Path :: get_tracking_path() . 'lib/main_tracker.class.php';

class WeblcmsLpiAttemptTracker extends MainTracker
{
	const PROPERTY_LP_ITEM_ID = 'lp_item_id';
	const PROPERTY_LP_VIEW_ID = 'lp_view_id';
	const PROPERTY_START_TIME = 'start_time';
	const PROPERTY_END_TIME = 'end_time';
	const PROPERTY_SCORE = 'score';
	const PROPERTY_STATUS = 'status';
	
	/**
	 * Constructor sets the default values
	 */
    function WeblcmsLpiAttemptTracker() 
    {
    	parent :: MainTracker('weblcms_lpi_attempt');
    }
    
    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
    	foreach($parameters as $key => $parameter)
    	{ 
    		if($key != 'event' && $key != 'id')
    			$this->set_property($key, $parameter);
    	}
    	
    	$this->create();
    	
    	return $this->get_id();
    }
    
    /**
     * Inherited
     * @see MainTracker :: is_summary_tracker
     */
    function is_summary_tracker()
    {
    	return false;
    }
    
    /**
     * Inherited
     */
    function get_default_property_names()
    {
    	return array_merge(parent :: get_default_property_names(), array(self :: PROPERTY_LP_VIEW_ID, self :: PROPERTY_START_TIME,
    		self :: PROPERTY_LP_ITEM_ID, self :: PROPERTY_END_TIME, self :: PROPERTY_SCORE, self :: PROPERTY_STATUS));
    }

    function get_lp_view_id()
    {
    	return $this->get_property(self :: PROPERTY_LP_VIEW_ID);
    }
 
    function set_lp_view_id($lp_view_id)
    {
    	$this->set_property(self :: PROPERTY_LP_VIEW_ID, $lp_view_id);
    }
    
  	function get_start_time()
    {
    	return $this->get_property(self :: PROPERTY_START_TIME);
    }
 
    function set_start_time($start_time)
    {
    	$this->set_property(self :: PROPERTY_START_TIME, $start_time);
    }
    
	function get_lp_item_id()
    {
    	return $this->get_property(self :: PROPERTY_LP_ITEM_ID);
    }
 
    function set_lp_item_id($lp_item_id)
    {
    	$this->set_property(self :: PROPERTY_LP_ITEM_ID, $lp_item_id);
    }
    
    function get_end_time()
    {
    	return $this->get_property(self :: PROPERTY_END_TIME);
    }
 
    function set_end_time($end_time)
    {
    	$this->set_property(self :: PROPERTY_END_TIME, $end_time);
    }
    
 	function get_score()
    {
    	return $this->get_property(self :: PROPERTY_SCORE);
    }
 
    function set_score($score)
    {
    	$this->set_property(self :: PROPERTY_SCORE, $score);
    }
    
	function get_status()
    {
    	return $this->get_property(self :: PROPERTY_STATUS);
    }
 
    function set_status($status)
    {
    	$this->set_property(self :: PROPERTY_STATUS, $status);
    }
    
    function empty_tracker($event)
    {
    	$this->remove();
    }
}
?>