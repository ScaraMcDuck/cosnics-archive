<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package repository.learningobject.forum
 */
class ForumTopic extends LearningObject
{
	const PROPERTY_VIEWS = 'views';
	const PROPERTY_REPLY_COUNT = 'replies';
	const PROPERTY_LAST_POST_ID = 'last_post_id';
	const PROPERTY_STATUS = 'status';
	const PROPERTY_NOTIFY = 'notify';

	function get_view_count ()
	{
		return $this->get_additional_property(self :: PROPERTY_VIEW_COUNT);
	}
	function set_view_count ($views)
	{
		return $this->set_additional_property(self :: PROPERTY_VIEW_COUNT, $views);
	}
	function get_reply_count ()
	{
		return $this->get_additional_property(self :: PROPERTY_REPLY_COUNT);
	}
	function set_reply_count ($replies)
	{
		return $this->set_additional_property(self :: PROPERTY_REPLY_COUNT, $replies);
	}
	function get_last_post_id ()
	{
		return $this->get_additional_property(self :: PROPERTY_LAST_POST_ID);
	}
	function set_last_post_id ($last_post_id)
	{
		return $this->set_additional_property(self :: PROPERTY_LAST_POST_ID, $last_post_id);
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