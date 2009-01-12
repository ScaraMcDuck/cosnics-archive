<?php

require_once dirname(__FILE__).'/blog_tool.class.php';

class BlogToolComponent extends ToolComponent
{
	static function factory ($component_name, $learning_path_tool) 
	{
		return parent :: factory('Blog', $component_name, $learning_path_tool);
	}

	function display_learning_object($object, $cloi_id)
	{
		return $this->get_tool()->display_learning_object($object, $cloi_id);
	}
	
}
?>