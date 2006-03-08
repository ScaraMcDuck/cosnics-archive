<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';

class Category extends LearningObject
{
	function get_parent_category_id ()
	{
		return $this->get_additional_property('parent');
	}
	function set_parent_category_id ($parent) 
	{
		return $this->set_additional_property('parent', $parent);
	}
}
?>