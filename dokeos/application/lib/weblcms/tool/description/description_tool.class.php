<?php
/**
 * $Id$
 * Description tool
 * @package application.weblcms.tool
 * @subpackage description
 */
//require_once dirname(__FILE__).'/../repository_tool.class.php';
//require_once dirname(__FILE__).'/description_browser.class.php';
require_once dirname(__FILE__).'/description_tool_component.class.php';

/**
 * This tool allows a user to publish descriptions in his or her course.
 */
class DescriptionTool extends Tool
{
	const ACTION_VIEW_DESCRIPTIONS = 'view';
	// Inherited.
	function run()
	{
		$action = $this->get_action();
		$component = parent :: run();
		
		if($component) return;
		
		switch ($action)
		{
			case self :: ACTION_VIEW_DESCRIPTIONS :
				$component = DescriptionToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_PUBLISH :
				$component = DescriptionToolComponent :: factory('Publisher', $this);
				break;
			default :
				$component = DescriptionToolComponent :: factory('Viewer', $this);
		}
		$component->run();
	}
}
?>