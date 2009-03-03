<?php
/**
 * @package repository.learningobject
 * @subpackage forum
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';

class ComplexForum extends ComplexLearningObjectItem
{	
	function get_allowed_types()
	{
		return array('forum','forum_topic');
	}
	
	function create()
	{
		parent :: create();
		
		$rdm = RepositoryDataManager :: get_instance();
		$lo = $rdm->retrieve_learning_object($this->get_ref());

		$parent = $rdm->retrieve_learning_object($this->get_parent());
		$parent->add_topic($lo->get_total_topics());
		$parent->add_post($lo->get_total_posts());
		//$parent->recalculate_last_post();
	}
	
	function delete()
	{
		parent :: delete();
		
		$rdm = RepositoryDataManager :: get_instance();
		$lo = $rdm->retrieve_learning_object($this->get_ref());

		$parent = $rdm->retrieve_learning_object($this->get_parent());
		$parent->remove_topic($lo->get_total_topics());
		$parent->remove_post($lo->get_total_posts());
		//$parent->recalculate_last_post();
	}
}
?>