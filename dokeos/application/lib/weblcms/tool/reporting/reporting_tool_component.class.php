<?php
require_once dirname(__FILE__) . '/../tool_component.class.php';

class ReportingToolComponent extends ToolComponent
{
	static function factory($component_name, $announcement_tool)
	{
		return parent :: factory('Reporting', $component_name, $announcement_tool);
	}
}