<?php
/**
 * $Id: wiki_tool.class.php 16640 2008-10-29 11:12:07Z Scara84 $
 * Wiki tool
 * @package application.weblcms.tool
 * @subpackage wiki
 */

require_once dirname(__FILE__).'/wiki_tool_component.class.php';
/**
 * This tool allows a user to publish wikis in his or her course.
 */
class WikiTool extends Tool
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
				$component = WikiToolComponent :: factory('Viewer', $this);
				break;
			default :
				$component = WikiToolComponent :: factory('Viewer', $this);
		}
		$component->run();
	}
}
?>