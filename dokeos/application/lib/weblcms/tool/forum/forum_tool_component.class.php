<?php
/**
 * $Id$
 * Repository tool
 * @package application.weblcms.tool.forum
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';
/**
==============================================================================
 *	This is the base class component for all forum tool components.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class ForumToolComponent extends ToolComponent
{
	static function factory($component_name, $forum_tool)
	{
		return parent :: factory('Forum', $component_name, $forum_tool);
	}
}