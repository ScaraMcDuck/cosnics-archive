<?php
/**
 * @package repository.learningobject
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../learningobject.class.php';
/**
 * This class represents a discussion forum.
 */
class Forum extends LearningObject
{
	/**
	 * Gets the number of topics in this forum
	 * @return int The number of topics
	 */
	function get_topic_count()
	{
		$datamanager = RepositoryDataManager::get_instance();
		$count = $datamanager->count_learning_objects('forum_topic',new EqualityCondition(self::PROPERTY_PARENT_ID, $this->get_id()));
		return $count;
	}
	/**
	 * Gets the number of posts in this forum
	 * @return int The number of posts
	 */
	function get_post_count()
	{
		//TODO: implement this in a more efficient way
		$topics = $this->get_forum_topics();
		$count = 0;
		while($topic = $topics->next_result())
		{
			$count += $topic->get_post_count();
		}
		return $count;
	}
	/**
	 * Gets the most recent post in this forum
	 * @return null|ForumPost If no posts in this forum, null will be returned.
	 * Else the most recent post.
	 */
	function get_last_post()
	{
		//TODO: implement this in a more efficient way
		$topics = $this->get_forum_topics();
		$last_post = null;
		while($topic = $topics->next_result())
		{
			$last_topic_post = $topic->get_last_post();
			if($last_post == null || $last_topic_post->get_creation_date() > $last_post->get_creation_date())
			{
				$last_post = $last_topic_post;
			}
		}
		return $last_post;
	}
	/**
	 * Gets the list of topics in this forum
	 * @return ResultSet The set of topics
	 */
	function get_forum_topics()
	{
		$datamanager = RepositoryDataManager::get_instance();
		$topics = $datamanager->retrieve_learning_objects('forum_topic',new EqualityCondition(self::PROPERTY_PARENT_ID, $this->get_id()));
		return $topics;
	}
	/**
	 * Gets a topic in this forum
	 * @param int $topic_id The id of the requested topic
	 * @return ForumTopic The topic
	 */
	function get_forum_topic($topic_id)
	{
		$datamanager = RepositoryDataManager::get_instance();
		$topic = $datamanager->retrieve_learning_object($topic_id);
		return $topic;
	}
}
?>