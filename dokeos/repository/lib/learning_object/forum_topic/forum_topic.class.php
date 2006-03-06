<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';

class ForumTopic extends LearningObject {
	function get_views () {
		return $this->get_additional_property('views');
	}
	function set_views ($views) {
		return $this->set_additional_property('views', $views);
	}
	function get_replies () {
		return $this->get_additional_property('replies');
	}
	function set_replies ($replies) {
		return $this->set_additional_property('replies', $replies);
	}
	function get_last_post_id () {
		return $this->get_additional_property('last_post_id');
	}
	function set_last_post_id ($last_post_id) {
		return $this->set_additional_property('last_post_id', $last_post_id);
	}
	function get_forum_id () {
		return $this->get_additional_property('forum_id');
	}
	function set_forum_id ($forum_id) {
		return $this->set_additional_property('forum_id', $forum_id);
	}
	function get_status () {
		return $this->get_additional_property('status');
	}
	function set_status ($status) {
		return $this->set_additional_property('status', $status);
	}
	function get_notify () {
		return $this->get_additional_property('notify');
	}
	function set_notify ($notify) {
		return $this->set_additional_property('notify', $notify);
	}
}
?>