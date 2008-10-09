<?php
/**
 * $Id: announcementtool.class.php 9200 2006-09-04 13:40:47Z bmol $
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage exercise
 */
//require_once dirname(__FILE__).'/../repository_tool.class.php';
require_once dirname(__FILE__).'/exercise_tool_component.class.php';
/**
 * This tool allows a user to publish exercises in his or her course.
 */
class ExerciseTool extends Tool
{
	const ACTION_VIEW_EXERCISES = 'view';
	/*
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_action();
		$component = parent :: run();
		
		if($component) return;
		
		switch($action) 
		{
			case self :: ACTION_PUBLISH:
				$component = ExerciseToolComponent :: factory('Publisher', $this);
				break;
			case self :: ACTION_VIEW_EXERCISES:
				$component = ExerciseToolComponent :: factory('Viewer', $this);
				break;
			default:
				$component = ExerciseToolComponent :: factory('Viewer', $this);
				break;
		}
		
		$component->run();
	}
}
?>