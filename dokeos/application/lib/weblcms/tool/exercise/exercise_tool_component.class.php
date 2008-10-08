<?php
/**
 * @package application.weblcms.tool.exercise
 */

class ExerciseToolComponent extends ToolComponent 
{
	static function factory ($component_name, $exercise_tool) 
	{
		return parent :: factory('Exercise', $component_name, $exercise_tool);
	}
}

?>