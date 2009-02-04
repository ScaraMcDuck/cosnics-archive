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
		return array(self :: PROPERTY_LOCKED);
	}
	
	function get_allowed_types()
	{
		return array('forum','forum_topic');
	}
}
?>