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
	const ACTION_BROWSE_FORUMS = 'browse';
	const ACTION_VIEW_FORUM = 'view';
	const ACTION_VIEW_TOPIC = 'view_topic';
	const ACTION_PUBLISH_FORUM = 'publish';
	
	const ACTION_CREATE_FORUM_POST = 'add_post';
	const ACTION_EDIT_FORUM_POST = 'edit_post';
	const ACTION_DELETE_FORUM_POST = 'delete_post';
	const ACTION_QUOTE_FORUM_POST = 'quote_post';
	
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
			case self :: ACTION_BROWSE_FORUMS :
				$component = ForumToolComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_PUBLISH_FORUM :
				$component = ForumToolComponent :: factory('Publisher', $this);
				break;
			case self :: ACTION_VIEW_FORUM :
				$component = ForumToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_VIEW_TOPIC :
				$component = ForumToolComponent :: factory('TopicViewer', $this);
				break;
			case self :: ACTION_CREATE_FORUM_POST :
				$component = ForumToolComponent :: factory('PostCreator', $this);
				break;
			case self :: ACTION_EDIT_FORUM_POST :
				$component = ForumToolComponent :: factory('PostDeleter', $this);
				break;
			case self :: ACTION_DELETE_FORUM_POST :
				$component = ForumToolComponent :: factory('PostDeleter', $this);
				break;
			case self :: ACTION_QUOTE_FORUM_POST :
				$component = ForumToolComponent :: factory('PostQuoter', $this);
				break;
			default :
				$component = ForumToolComponent :: factory('Browser', $this);
		}
		$component->run();
	}
}
?>