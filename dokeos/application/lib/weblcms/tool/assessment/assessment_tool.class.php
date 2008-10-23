<?php
/**
 * $Id: announcementtool.class.php 9200 2006-09-04 13:40:47Z bmol $
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage exercise
 */
//require_once dirname(__FILE__).'/../repository_tool.class.php';
require_once dirname(__FILE__).'/assessment_tool_component.class.php';
/**
 * This tool allows a user to publish exercises in his or her course.
 */
class AssessmentTool extends Tool
{
	const ACTION_VIEW_ASSESSMENTS = 'view';
	const ACTION_TAKE_ASSESSMENT = 'exec';
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
				$component = AssessmentToolComponent :: factory('Publisher', $this);
				break;
			case self :: ACTION_VIEW_ASSESSMENTS:
				$component = AssessmentToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_TAKE_ASSESSMENT:
				$component = AssessmentToolComponent :: factory('Tester', $this);
				break;
			default:
				$component = AssessmentToolComponent :: factory('Viewer', $this);
				break;
		}
		
		$component->run();
	}
}
?>