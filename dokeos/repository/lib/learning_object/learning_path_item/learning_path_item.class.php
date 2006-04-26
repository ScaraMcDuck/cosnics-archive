<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
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
	function is_ordered()
	{
		return true;
	}
}
?>