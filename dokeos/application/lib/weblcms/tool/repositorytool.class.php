<?php
require_once dirname(__FILE__) . '/tool.class.php';

abstract class RepositoryTool extends Tool
{
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	
	function get_course_id()
	{
		return $this->get_parent()->get_course_id();
	}
	
	function get_groups()
	{
		return $this->get_parent()->get_groups();
	}
	
	function get_categories()
	{
		return $this->get_parent()->get_categories();
	}
}
?>