<?php
/**
 * @package repository.learningobject
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../learning_object.class.php';
/**
 * This class represents a discussion forum.
 */
class Forum extends LearningObject
{
	const PROPERTY_LOCKED = 'locked';
	const PROPERTY_TOTAL_TOPICS = 'total_topics';
	const PROPERTY_TOTAL_POSTS = 'total_posts';
	const PROPERTY_LAST_POST = 'last_post';
	
	function get_locked()
	{
		return $this->get_additional_property(self :: PROPERTY_LOCKED);
	}
	 
	function set_locked($locked)
	{
		return $this->set_additional_property(self :: PROPERTY_LOCKED, $locked);
	}
	
	function get_total_topics()
	{
		return $this->get_additional_property(self :: PROPERTY_TOTAL_TOPICS);
	}
	 
	function set_total_topics($total_topics)
	{
		$this->set_additional_property(self :: PROPERTY_TOTAL_TOPICS, $total_topics);
	}
	
	function get_total_posts()
	{
		return $this->get_additional_property(self :: PROPERTY_TOTAL_POSTS);
	}
	 
	function set_total_posts($total_posts)
	{
		$this->set_additional_property(self :: PROPERTY_TOTAL_POSTS, $total_posts);
	}
	
	function get_last_post()
	{
		return $this->get_additional_property(self :: PROPERTY_LAST_POST);
	}
	 
	function set_last_post($last_post)
	{
		$this->set_additional_property(self :: PROPERTY_LAST_POST, $last_post);
	}
	
	function add_last_post($last_post)
	{
		$this->set_last_post($last_post);
		$this->update();
		
		$rdm = RepositoryDataManager :: get_instance();
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $this->get_id());
		$wrappers = $rdm->retrieve_complex_learning_object_items($condition);
		
		while($item = $wrappers->next_result())
		{
			$lo = $rdm->retrieve_learning_object($item->get_parent());
			$lo->add_last_post($last_post);
		}
	}
	
	function recalculate_last_post($child_id)
	{
		$rdm = RepositoryDataManager :: get_instance();

                $children = $rdm->retrieve_last_post($this->get_id(),$child_id);
		
//		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->get_id());
//
//                $order_property[] = new ObjectTableOrder('add_date',SORT_DESC);
//		$children = $rdm->retrieve_complex_learning_object_items($condition, $order_property,array(), 0, 1);
		$lp = $children->next_result();
		
		$id = ($lp)?$lp->get_id():0;
                
		if($this->get_last_post() != $id)
		{
			$this->set_last_post($id);
			$this->update();
			
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $this->get_id());
			$wrappers = $rdm->retrieve_complex_learning_object_items($condition);
			
			while($item = $wrappers->next_result())
			{
				$lo = $rdm->retrieve_learning_object($item->get_parent());
				$lo->recalculate_last_post();
			}
		}
	}
	
	static function get_additional_property_names()
	{
		return array(self :: PROPERTY_LOCKED, self :: PROPERTY_TOTAL_TOPICS, self :: PROPERTY_TOTAL_POSTS, self :: PROPERTY_LAST_POST);
	}
	
	function get_allowed_types()
	{
		return array('forum','forum_topic');
	}
	
	function add_post($posts = 1)
	{
		$this->set_total_posts($this->get_total_posts() + $posts);
		$this->update();
		
		$rdm = RepositoryDataManager :: get_instance();
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $this->get_id());
		$wrappers = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
		
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
		$wrappers = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
		
		while($item = $wrappers->next_result())
		{
			$lo = $rdm->retrieve_learning_object($item->get_parent());
			$lo->remove_post($posts);
		}
	}
	
	function add_topic($topics = 1)
	{
		$this->set_total_topics($this->get_total_topics() + $topics);
		$this->update();
		
		$rdm = RepositoryDataManager :: get_instance();
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $this->get_id());
		$wrappers = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
		
		while($item = $wrappers->next_result())
		{
			$lo = $rdm->retrieve_learning_object($item->get_parent());
			$lo->add_topic($topics);
		}
	}
	
	function remove_topic($topics = 1)
	{
		$this->set_total_topics($this->get_total_topics() - $topics);
		$this->update();
		
		$rdm = RepositoryDataManager :: get_instance();
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $this->get_id());
		$wrappers = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
		
		while($item = $wrappers->next_result())
		{
			$lo = $rdm->retrieve_learning_object($item->get_parent());
			$lo->remove_topic($topics);
		}
	}
}
?>