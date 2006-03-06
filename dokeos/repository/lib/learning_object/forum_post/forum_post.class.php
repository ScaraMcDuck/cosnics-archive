<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';

class ForumPost extends LearningObject {
	function get_topic_id () {
		return $this->get_additional_property('topic_id');
	}
	function set_topic_id ($topic_id) {
		return $this->set_additional_property('topic_id', $topic_id);
	}
	function get_topic_notify () {
		return $this->get_additional_property('topic_notify');
	}
	function set_topic_notify ($topic_notify) {
		return $this->set_additional_property('topic_notify', $topic_notify);
	}
}
?>