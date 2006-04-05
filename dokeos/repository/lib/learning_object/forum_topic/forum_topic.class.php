<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';

class ForumTopic extends LearningObject
{
	const PROPERTY_VIEWS = 'views';
	const PROPERTY_REPLIES = 'replies';
	const PROPERTY_LAST_POST_ID = 'last_post_id';
	const PROPERTY_FORUM_ID = 'forum_id';
	const PROPERTY_STATUS = 'status';
	const PROPERTY_NOTIFY = 'notify';
	function get_views ()
	{
		return $this->get_additional_property(self :: PROPERTY_VIEWS);
	}
	function set_views ($views) 
	{
		return $this->set_additional_property(self :: PROPERTY_VIEWS, $views);
	}
	function get_replies ()
	{
		return $this->get_additional_property(self :: PROPERTY_REPLIES);
	}
	function set_replies ($replies) 
	{
		return $this->set_additional_property(self :: PROPERTY_REPLIES, $replies);
	}
	function get_last_post_id ()
	{
		return $this->get_additional_property(self :: PROPERTY_LAST_POST_ID);
	}
	function set_last_post_id ($last_post_id) 
	{
		return $this->set_additional_property(self :: PROPERTY_LAST_POST_ID, $last_post_id);
	}
	function get_forum_id ()
	{
		return $this->get_additional_property(self :: PROPERTY_FORUM_ID);
	}
	function set_forum_id ($forum_id) 
	{
		return $this->set_additional_property(self :: PROPERTY_FORUM_ID, $forum_id);
	}
	function get_status ()
	{
		return $this->get_additional_property(self :: PROPERTY_STATUS);
	}
	function set_status ($status) 
	{
		return $this->set_additional_property(self :: PROPERTY_STATUS, $status);
	}
	function get_notify ()
	{
		return $this->get_additional_property(self :: PROPERTY_NOTIFY);
	}
	function set_notify ($notify) 
	{
		return $this->set_additional_property(self :: PROPERTY_NOTIFY, $notify);
	}
}
?>