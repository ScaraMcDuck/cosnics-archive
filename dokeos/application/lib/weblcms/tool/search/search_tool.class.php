<?php
/**
 * $Id: course_settings_tool.class.php 16605 2008-10-23 10:50:07Z vanpouckesven $
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage course_settings
 */

require_once dirname(__FILE__).'/search_tool_component.class.php';
/**
 * This tool allows a user to publish course_settingss in his or her course.
 */
class SearchTool extends Tool
{
	const ACTION_SEARCH = 'search';
	
	/**
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_action();
		$component = parent :: run();
		
		if($component) return;
		
		switch ($action)
		{
			case self :: ACTION_SEARCH :
				$component = SearchToolComponent :: factory('Searcher', $this);
				break;
			default :
				$component = SearchToolComponent :: factory('Searcher', $this);
		}
		$component->run();
	}
}
?>