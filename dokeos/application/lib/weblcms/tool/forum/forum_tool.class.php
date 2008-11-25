<?php
/**
 * $Id: forum_tool.class.php 16640 2008-10-29 11:12:07Z Scara84 $
 * Forum tool
 * @package application.weblcms.tool
 * @subpackage forum
 */

require_once dirname(__FILE__).'/forum_tool_component.class.php';
/**
 * This tool allows a user to publish forums in his or her course.
 */
class ForumTool extends Tool
{
	const ACTION_VIEW_ANNOUNCEMENTS = 'view';
	
	/**
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_action();
		$component = parent :: run();
		
		if($component)
		{
			return;
		}
		
		switch ($action)
		{
			case self :: ACTION_VIEW_ANNOUNCEMENTS :
				$component = ForumToolComponent :: factory('Viewer', $this);
				break;
			default :
				$component = ForumToolComponent :: factory('Viewer', $this);
		}
		$component->run();
	}
}
?>