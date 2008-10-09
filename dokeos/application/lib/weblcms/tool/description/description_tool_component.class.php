<?php
/**
 * $Id$
 * Repository tool
 * @package application.weblcms.tool.description
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';
/**
==============================================================================
 *	This is the base class component for all description tool components.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class DescriptionToolComponent extends ToolComponent
{
	static function factory($component_name, $announcement_tool)
	{
		return parent :: factory('Description', $component_name, $announcement_tool);
	}
}