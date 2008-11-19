<?php
/**
 * $Id: announcementtool.class.php 9200 2006-09-04 13:40:47Z bmol $
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage assessment
 */
//require_once dirname(__FILE__).'/../repository_tool.class.php';
require_once dirname(__FILE__).'/assessment_tool_component.class.php';
/**
 * This tool allows a user to publish assessments in his or her course.
 */
class AssessmentTool extends Tool
{
	const ACTION_VIEW_ASSESSMENTS = 'view';
	const ACTION_VIEW_USER_ASSESSMENTS = 'view_user';
	const ACTION_TAKE_ASSESSMENT = 'take';
	const ACTION_VIEW_RESULTS = 'result';
	
	const PARAM_USER_ASSESSMENT = 'uaid';
	const PARAM_ASSESSMENT = 'aid';
	const PARAM_ADD_FEEDBACK = 'feedback';
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
			case self :: ACTION_VIEW_RESULTS:
				$component = AssessmentToolComponent :: factory('ResultsViewer', $this);
				break;
			default:
				$component = AssessmentToolComponent :: factory('Viewer', $this);
				break;
		}
		
		$component->run();
	}
}
?>