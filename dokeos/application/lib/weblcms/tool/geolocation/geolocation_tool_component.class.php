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

class GeolocationToolComponent extends ToolComponent
{
	static function factory($component_name, $tool)
	{
		return parent :: factory('Geolocation', $component_name, $tool);
	}
}