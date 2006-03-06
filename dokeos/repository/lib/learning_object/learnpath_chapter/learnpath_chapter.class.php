<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';

class LearnpathChapter extends LearningObject 
{
	function get_display_order () 
	{
		return $this->get_additional_property('display_order');
	}
	function set_display_order ($display_order) 
	{
		return $this->set_additional_property('display_order', $display_order);
	}
}
?>