<?php
require_once dirname(__FILE__).'/../../learningobject.class.php';
/**
 * @package repository.learningobject
 * @subpackage forum
 */
class Forum extends LearningObject
{
	const PROPERTY_TOPIC_COUNT = 'topics';
	const PROPERTY_POST_COUNT = 'posts';
	const PROPERTY_LAST_POST_ID = 'last_post_id';
	const PROPERTY_CATEGORY_ID = 'category_id';

	function get_topic_count()
	{
		return $this->get_additional_property(self :: PROPERTY_TOPIC_COUNT);
	}
	function set_topic_count($topics)
	{
		return $this->set_additional_property(self :: PROPERTY_TOPIC_COUNT, $topics);
	}
	function get_post_count()
	{
		return $this->get_additional_property(self :: PROPERTY_POST_COUNT);
	}
	function set_post_count($posts)
	{
		return $this->set_additional_property(self :: PROPERTY_POST_COUNT, $posts);
	}
	function get_last_post_id()
	{
		return $this->get_additional_property(self :: PROPERTY_LAST_POST_ID);
	}
	function set_last_post_id($last_post_id)
	{
		return $this->set_additional_property(self :: PROPERTY_LAST_POST_ID, $last_post_id);
	}
	function get_category_id()
	{
		return $this->get_additional_property(self :: PROPERTY_CATEGORY_ID);
	}
	function set_category_id($cat_id)
	{
		return $this->set_additional_property(self :: PROPERTY_CATEGORY_ID, $cat_id);
	}
}
?>