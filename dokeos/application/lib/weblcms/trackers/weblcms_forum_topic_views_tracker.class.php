<?php

/**
 * @package users.lib.trackers
 */
 
require_once Path :: get_tracking_path() . 'lib/main_tracker.class.php';

class WeblcmsForumTopicViewsTracker extends MainTracker
{	
	// Can be used for subscribsion of users / classes
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_DATE = 'date';
	const PROPERTY_PUBLICATION_ID = 'publication_id';
	const PROPERTY_FORUM_TOPIC_ID = 'forum_topic_id';
	
	/**
	 * Constructor sets the default values
	 */
    function WeblcmsForumTopicViewsTracker() 
    {
    	parent :: MainTracker('weblcms_forum_topic_views');
    }
    
    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
    	$user = $parameters['user_id'];
    	$publication = $parameters['publication_id'];
    	$forum_topic = $parameters['forum_topic_id'];
    	
    	$this->set_user_id($user);
    	$this->set_publication_id($publication);
    	$this->set_forum_topic_id($forum_topic);
    	$this->set_date(DatabaseRepositoryDataManager :: to_db_date(time()));
    	
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
    	return array_merge(parent :: get_default_property_names(), array(self :: PROPERTY_USER_ID, self :: PROPERTY_PUBLICATION_ID,
    		self :: PROPERTY_FORUM_TOPIC_ID, self :: PROPERTY_DATE));
    }

    function get_user_id()
    {
    	return $this->get_property(self :: PROPERTY_USER_ID);
    }
 
    function set_user_id($user_id)
    {
    	$this->set_property(self :: PROPERTY_USER_ID, $user_id);
    }
    
	function get_publication_id()
    {
    	return $this->get_property(self :: PROPERTY_PUBLICATION_ID);
    }
 
    function set_publication_id($publication_id)
    {
    	$this->set_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }
    
    function get_forum_topic_id()
    {
    	return $this->get_property(self :: PROPERTY_FORUM_TOPIC_ID);
    }
 
    function set_forum_topic_id($forum_topic_id)
    {
    	$this->set_property(self :: PROPERTY_FORUM_TOPIC_ID, $forum_topic_id);
    }
    
    function get_date()
    {
    	return $this->get_property(self :: PROPERTY_DATE);
    }
 
    function set_date($date)
    {
    	$this->set_property(self :: PROPERTY_DATE, $date);
    }
    
    function empty_tracker($event)
    {
    	$this->remove();
    }

}
?>