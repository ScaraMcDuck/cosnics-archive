<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';

class LearningPathItem extends LearningObject 
{
	function get_object_id () 
	{
		return $this->get_additional_property('object_id');
	}
	function set_object_id ($object_id) 
	{
		return $this->set_additional_property('object_id', $object_id);
	}
}
?>