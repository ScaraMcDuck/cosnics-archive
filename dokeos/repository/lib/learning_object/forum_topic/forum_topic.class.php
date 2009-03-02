<?php
/**
 * @package repository.learningobject
 * @subpackage forum
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents a topic in a discussion forum.
 */
class ForumTopic extends LearningObject
{
	const PROPERTY_LOCKED = 'locked';
	const PROPERTY_TOTAL_POSTS = 'total_posts';

	function get_locked()
	{
		return $this->get_additional_property(self :: PROPERTY_LOCKED);
	}
	
	function set_locked($locked)
	{
		return $this->set_additional_property(self :: PROPERTY_LOCKED, $locked);
	}
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_LOCKED, self :: PROPERTY_TOTAL_POSTS);
	}
	
	function get_allowed_types()
	{
		return array('forum_post');
	}
	
	function get_total_posts()
	{
		return $this->get_additional_property(self :: PROPERTY_TOTAL_POSTS);
	}
	 
	function set_total_posts($total_posts)
	{
		return $this->set_additional_property(self :: PROPERTY_TOTAL_POSTS, $total_posts);
	}
	
	function add_post($posts = 1)
	{
		$this->set_total_posts($this->get_total_posts() + $posts);
		$this->update();
		
		$rdm = RepositoryDataManager :: get_instance();
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $this->get_id());
		$wrappers = $rdm->retrieve_complex_learning_object_items($condition);
		
		while($item = $wrappers->next_result())
		{
			$lo = $rdm->retrieve_learning_object($item->get_parent());
			$lo->add_post($posts);
		}
	}
	
	function remove_post($posts = 1)
	{
		$this->set_total_posts($this->get_total_posts() - $posts);
		$this->update();
		
		$rdm = RepositoryDataManager :: get_instance();
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $this->get_id());
		$wrappers = $rdm->retrieve_complex_learning_object_items($condition);
		
		while($item = $wrappers->next_result())
		{
			$lo = $rdm->retrieve_learning_object($item->get_parent());
			$lo->remove_post($posts);
		}
	}
	
}
?>