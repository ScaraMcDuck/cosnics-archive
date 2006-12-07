<?php
/**
 * @package repository.learningobject
 * @subpackage forum
 */
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * This class represents a topic in a discussion forum.
 */
class ForumTopic extends LearningObject
{
	const PROPERTY_LOCKED = 'locked';
	/**
	 * Gets the number of replies in this topic.
	 * This is the number of posts minus one.
	 * @return int The number of replies.
	 */
	function get_reply_count ()
	{
		$reply_count = ($this->get_post_count())-1;
		return $reply_count >= 0 ? $reply_count : 0;
	}
	/**
	 * Gets the most recent post in this topic.
	 * @return ForumPost The most recent post in this topic.
	 */
	function get_last_post()
	{
		$datamanager = RepositoryDataManager::get_instance();
		$posts = $datamanager->retrieve_learning_objects('forum_post',new EqualityCondition(self::PROPERTY_PARENT_ID, $this->get_id()), array('created'),array(SORT_ASC),0,1);
		return $posts->next_result();
	}
	/**
	 * Gets the posts in this topic
	 * @return ResultSet A result set with all posts in this topic
	 */
	function get_forum_posts()
	{
		$datamanager = RepositoryDataManager::get_instance();
		$posts = $datamanager->retrieve_learning_objects('forum_post',new EqualityCondition(self::PROPERTY_PARENT_ID, $this->get_id()), array('created'), array(SORT_ASC));
		return $posts;
	}
	/**
	 * Gets the number of posts in this topic
	 * @return int The number of posts in this topic
	 */
	function get_post_count()
	{
		$datamanager = RepositoryDataManager::get_instance();
		$post_count = $datamanager->count_learning_objects('forum_post',new EqualityCondition(self::PROPERTY_PARENT_ID, $this->get_id()));
		return $post_count;
	}
	/**
	 * Determines if this forum topic is locked
	 */
	function is_locked()
	{
		return $this->get_additional_property(self :: PROPERTY_LOCKED);
	}
	/**
	 * Locks this forum topic
	 * @param boolean $locked
	 */
	function set_locked($locked)
	{
		return $this->set_additional_property(self :: PROPERTY_LOCKED, $locked);
	}
	/**
	 * When creating a new forum topic, a first forum post in that topic will
	 * also be created. This post has the exact same properties as the topic
	 * (title, description, owner,...)
	 */
	function create()
	{
		parent::create();
		$post = new ForumPost();
		$post->set_title($this->get_title());
		$post->set_description($this->get_description());
		$post->set_parent_id($this->get_id());
		$post->set_owner_id($this->get_owner_id());
		$post->create();
	}
	// Inherited
	function is_master_type()
	{
		return false;
	}
}
?>