<?php
/**
 * @package repository.learningobject
 * @subpackage forum_topic
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';

class ComplexForumTopic extends ComplexLearningObjectItem
{
	const PROPERTY_TYPE = 'type';
	
	function get_type() 
	{
		return $this->get_additional_property(self :: PROPERTY_TYPE);
	}
	
	function set_type($type)
	{
		$this->set_additional_property(self :: PROPERTY_TYPE, $type);
	}
	
	static function get_additional_property_names()
	{
		return array(self :: PROPERTY_TYPE);
	}
	
	function create()
	{	
		parent :: create();
		
		$rdm = RepositoryDataManager :: get_instance();
		$lo = $rdm->retrieve_learning_object($this->get_ref());

		$parent = $rdm->retrieve_learning_object($this->get_parent());
		$parent->add_topic();
		$parent->add_post($lo->get_total_posts());
	}
	
	function delete()
	{
		parent :: delete();
		
		$rdm = RepositoryDataManager :: get_instance();
		$lo = $rdm->retrieve_learning_object($this->get_ref());

		$parent = $rdm->retrieve_learning_object($this->get_parent());
		$parent->remove_topic();
		$parent->remove_post($lo->get_total_posts());
	}
	
	function get_allowed_types()
	{
		return array('forum_post');
	}
}
?>