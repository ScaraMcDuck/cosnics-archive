<?php
/**
 * $Id: user_tool.class.php 16471 2008-10-09 07:46:00Z SSJMaglor $
 * User tool
 * @package application.weblcms.tool
 * @subpackage user
 */

require_once dirname(__FILE__).'/user_tool_component.class.php';
/**
 * This tool allows a user to publish users in his or her course.
 */
class UserTool extends Tool
{
	const ACTION_SUBSCRIBE_USERS = 'subscribe';
	const ACTION_UNSUBSCRIBE_USERS = 'unsubscribe';
	const ACTION_USER_DETAILS = 'user_details';
	
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
			case self :: ACTION_SUBSCRIBE_USERS :
				$component = UserToolComponent :: factory('SubscribeBrowser', $this);
				break;
			case self :: ACTION_UNSUBSCRIBE_USERS :
				$component = UserToolComponent :: factory('UnsubscribeBrowser', $this);
				break;
			case self :: ACTION_USER_DETAILS :
				$component = UserToolComponent :: factory('Details', $this);
				break;
			default :
				$component = UserToolComponent :: factory('UnsubscribeBrowser', $this);
		}
		$component->run();
	}
}
?>