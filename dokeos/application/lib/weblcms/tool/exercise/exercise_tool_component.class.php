<?php
/**
 * @package application.weblcms.tool.exercise
 */

/**
 * The base class for all exercise tool components.
 *
 */
class ExerciseToolComponent extends ToolComponent 
{
	/**
	 * Inherited
	 *
	 * @param unknown_type $component_name
	 * @param unknown_type $exercise_tool
	 * @return unknown
	 */
	static function factory ($component_name, $exercise_tool) 
	{
		return parent :: factory('Exercise', $component_name, $exercise_tool);
	}
	
	function get_toolbar() 
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url());
		$action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Publish'), Theme :: get_common_img_path().'action_publish.png', $this->get_url(array(ExerciseTool :: PARAM_ACTION => ExerciseTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
		
		$action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Browse'), Theme :: get_common_img_path().'action_browser.png', $this->get_url(array(ExerciseTool :: PARAM_ACTION => ExerciseTool :: ACTION_VIEW_EXERCISES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
		
		return $action_bar;
	}
	
}

?>