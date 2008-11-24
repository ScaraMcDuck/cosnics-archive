<?php
/**
 * $Id$
 * Repository tool
 * @package application.weblcms.tool.announcement
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';
/**
==============================================================================
 *	This is the base class component for all announcement tool components.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class CourseSectionsToolComponent extends ToolComponent
{
	static function factory($component_name, $course_sections_tool)
	{
		return parent :: factory('CourseSections', $component_name, $course_sections_tool);
	}
}