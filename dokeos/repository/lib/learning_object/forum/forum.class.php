<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package learningobject.forum
 */
class Forum extends LearningObject
{
	function get_topics () {
		return $this->get_additional_property('topics');
	}
	function set_topics ($topics) {
		return $this->set_additional_property('topics', $topics);
	}
	function get_posts () {
		return $this->get_additional_property('posts');
	}
	function set_posts ($posts) {
		return $this->set_additional_property('posts', $posts);
	}
	function get_last_post_id () {
		return $this->get_additional_property('last_post_id');
	}
	function set_last_post_id ($last_post_id) {
		return $this->set_additional_property('last_post_id', $last_post_id);
	}
	function get_cat_id () {
		return $this->get_additional_property('cat_id');
	}
	function set_cat_id ($cat_id) {
		return $this->set_additional_property('cat_id', $cat_id);
	}
	function get_forum_type () {
		return $this->get_additional_property('forum_type');
	}
	function set_forum_type ($forum_type) {
		return $this->set_additional_property('forum_type', $forum_type);
	}
}
?>