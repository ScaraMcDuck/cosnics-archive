<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';

class LearnpathItem extends LearningObject 
{
	function get_item_type () 
	{
		return $this->get_additional_property('item_type');
	}
	function set_item_type ($item_type) 
	{
		return $this->set_additional_property('item_type', $item_type);
	}
	function get_display_order () 
	{
		return $this->get_additional_property('display_order');
	}
	function set_display_order ($display_order) 
	{
		return $this->set_additional_property('display_order', $display_order);
	}
	function get_prereq_id () 
	{
		return $this->get_additional_property('prereq_id');
	}
	function set_prereq_id ($prereq_id) 
	{
		return $this->set_additional_property('prereq_id', $prereq_id);
	}
	function get_prereq_type () 
	{
		return $this->get_additional_property('prereq_type');
	}
	function set_prereq_type ($prereq_type) 
	{
		return $this->set_additional_property('prereq_type', $prereq_type);
	}
	function get_prereq_completion_limit () 
	{
		return $this->get_additional_property('prereq_completion_limit');
	}
	function set_prereq_completion_limit ($prereq_completion_limit) 
	{
		return $this->set_additional_property('prereq_completion_limit', $prereq_completion_limit);
	}
}
?>