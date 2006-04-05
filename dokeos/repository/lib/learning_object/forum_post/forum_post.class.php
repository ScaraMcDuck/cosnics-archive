<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';

class ForumPost extends LearningObject {
	const PROPERTY_TOPIC_ID = 'topic_id';
	const PROPERTY_TOPIC_NOTIFY = 'topic_notify';
	function get_topic_id () {
		return $this->get_additional_property(self :: PROPERTY_TOPIC_ID);
	}
	function set_topic_id ($topic_id) {
		return $this->set_additional_property(self :: PROPERTY_TOPIC_ID, $topic_id);
	}
	function get_topic_notify () {
		return $this->get_additional_property(self :: PROPERTY_TOPIC_NOTIFY);
	}
	function set_topic_notify ($topic_notify) {
		return $this->set_additional_property(self :: PROPERTY_TOPIC_NOTIFY, $topic_notify);
	}
}
?>