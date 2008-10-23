<?php
/**
 * $Id$
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage course_settings
 */

require_once dirname(__FILE__).'/statistics_tool_component.class.php';
/**
 * This tool allows a user to publish course_settingss in his or her course.
 */
class StatisticsTool extends Tool
{
	const ACTION_VIEW_STATISTICS = 'view';
	
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
			case self :: ACTION_VIEW_STATISTICS :
				$component = StatisticsToolComponent :: factory('Viewer', $this);
				break;
			default :
				$component = StatisticsToolComponent :: factory('Viewer', $this);
		}
		$component->run();
	}
}
?>