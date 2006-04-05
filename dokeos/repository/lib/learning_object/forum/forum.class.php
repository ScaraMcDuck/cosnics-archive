<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package learningobject.forum
 */
class Forum extends LearningObject
{
	const PROPERTY_TOPICS = 'topics';
	const PROPERTY_POSTS = 'posts';
	const PROPERTY_LAST_POST_ID = 'last_post_id';
	const PROPERTY_CAT_ID = 'cat_id';
	
	function get_topics () {
		return $this->get_additional_property(self :: PROPERTY_TOPICS);
	}
	function set_topics ($topics) {
		return $this->set_additional_property(self :: PROPERTY_TOPICS, $topics);
	}
	function get_posts () {
		return $this->get_additional_property(self :: PROPERTY_POSTS);
	}
	function set_posts ($posts) {
		return $this->set_additional_property(self :: PROPERTY_POSTS, $posts);
	}
	function get_last_post_id () {
		return $this->get_additional_property(self :: PROPERTY_LAST_POST_ID);
	}
	function set_last_post_id ($last_post_id) {
		return $this->set_additional_property(self :: PROPERTY_LAST_POST_ID, $last_post_id);
	}
	function get_cat_id () {
		return $this->get_additional_property(self :: PROPERTY_CAT_ID);
	}
	function set_cat_id ($cat_id) {
		return $this->set_additional_property(self :: PROPERTY_CAT_ID, $cat_id);
	}
}
?>