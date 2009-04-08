<?php
/**
 * @package repository.learningobject
 * @subpackage forum_post
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';

class ComplexForumPost extends ComplexLearningObjectItem
{
	const PROPERTY_REPLY_ON_POST = 'reply_on_post';
	
	function get_reply_on_post()
	{
		return $this->get_additional_property(self :: PROPERTY_REPLY_ON_POST);
	}
	
	function set_reply_on_post($reply_on_post)
	{
		$this->set_additional_property(self :: PROPERTY_REPLY_ON_POST, $reply_on_post);
	}
	
	static function get_additional_property_names()
	{
		return array(self :: PROPERTY_REPLY_ON_POST);
	}
	
	function create()
	{
		parent :: create();
		
		$parent = RepositoryDataManager :: get_instance()->retrieve_learning_object($this->get_parent());
		$parent->add_post();
		//$parent->add_last_post($this->get_id());
	}
	
	function delete()
	{
		parent :: delete();
		
		$datamanager = RepositoryDataManager :: get_instance();
		
		$siblings = $datamanager->count_complex_learning_object_items(new EqualityCondition('parent', $this->get_parent()));
		if($siblings == 0)
		{
			$wrappers = $datamanager->retrieve_complex_learning_object_items(new EqualityCondition('ref', $this->get_parent()));
			while($wrapper = $wrappers->next_result())
			{
				$wrapper->delete();
			}
			
			$datamanager->delete_learning_object_by_id($this->get_parent());
		}
		
		$parent = $datamanager->retrieve_learning_object($this->get_parent());
		$parent->remove_post();
		//$parent->recalculate_last_post();
	}
	
	/*function get_allowed_types()
	{
		return array('forum_post');
	}*/
}
?>