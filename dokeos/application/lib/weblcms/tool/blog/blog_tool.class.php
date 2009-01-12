<?php
/**
 * $Id: blog_tool.class.php 17649 2009-01-12 08:33:30Z vanpouckesven $
 * Learning path tool
 * @package application.weblcms.tool
 * @subpackage blog
 */

require_once dirname(__FILE__).'/blog_tool_component.class.php';
/**
 * This tool allows a user to publish learning paths in his or her course.
 */
class BlogTool extends Tool
{
	const ACTION_VIEW_BLOG = 'view';
	const ACTION_VIEW_BLOG_ITEM = 'view_item';
	const ACTION_BROWSE_BLOGS = 'browse';
	
	const PARAM_BLOG = 'blog';
	
	// Inherited.
	function run()
	{
		$action = $this->get_action();
		$component = parent :: run();
		
		if ($component) return;
		
		switch($action)
		{
			case self :: ACTION_PUBLISH:
				$component = BlogToolComponent :: factory('Publisher', $this);
				break;
			case self :: ACTION_VIEW_BLOG:
				$component = BlogToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_VIEW_BLOG_ITEM:
				$component = BlogToolComponent :: factory('ItemViewer', $this);
				break;
			case self :: ACTION_BROWSE_BLOGS:
				$component = BlogToolComponent :: factory('Browser', $this);
				break;
			default:
				$component = BlogToolComponent :: factory('Browser', $this);
				break;
		}
		
		$component->run();
	}
}
?>