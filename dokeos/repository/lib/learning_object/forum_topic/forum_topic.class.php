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
	const PROPERTY_LAST_POST = 'last_post';

	function create()
	{
		$succes = parent :: create();
		$children = RepositoryDataManager :: get_instance()->count_complex_learning_object_items(new EqualityCondition('parent', $this->get_id()));
				
		if($children == 0)
		{
			$learning_object = new AbstractLearningObject('forum_post', $this->get_owner_id());
			$learning_object->set_title($this->get_title());
			$learning_object->set_description($this->get_description());
			$learning_object->set_owner_id($this->get_owner_id());
			
			$learning_object->create();
			
			$attachments = $this->get_attached_learning_objects();
			foreach($attachments as $attachment)
				$learning_object->attach_learning_object($attachment->get_id());
			
			$cloi = ComplexLearningObjectItem :: factory('forum_post');

			$cloi->set_ref($learning_object->get_id());
			$cloi->set_user_id($this->get_owner_id());
			$cloi->set_parent($this->get_id());
			$cloi->set_display_order(1);
			
			$cloi->create();
		}
		
		return $succes;
	}
	
	function supports_attachments()
	{
		return true;
	}
	
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
	
	function recalculate_last_post()
	{
		$rdm = RepositoryDataManager :: get_instance();
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->get_id());
		$children = $rdm->retrieve_complex_learning_object_items($condition, array(new ObjectTableOrder('add_date')), array(SORT_DESC), 0, 1);
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