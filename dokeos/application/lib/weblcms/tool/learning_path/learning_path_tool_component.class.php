<?php

require_once dirname(__FILE__).'/learning_path_tool.class.php';

class LearningPathToolComponent extends ToolComponent
{
	static function factory ($component_name, $learning_path_tool) 
	{
		return parent :: factory('LearningPath', $component_name, $learning_path_tool);
	}
	
}
?>