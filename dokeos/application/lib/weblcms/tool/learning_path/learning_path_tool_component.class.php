<?php

require_once dirname(__FILE__).'/learning_path_tool.class.php';

class LearningPathToolComponent extends ToolComponent
{
	static function factory ($component_name, $learning_path_tool) 
	{
		return parent :: factory('LearningPath', $component_name, $learning_path_tool);
	}
	
	function get_toolbar() 
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url());
		$action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
		
		$action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Browse'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATHS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
		
		return $action_bar;
	}
}
?>