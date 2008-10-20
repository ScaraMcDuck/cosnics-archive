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
	
	/**
	 * Returns the actions that can be used on the left part of the action bar in exercise components.
	 *
	 * @return array: actions to be used in left part of the action bar.
	 */
	/*function get_left_actions() 
	{
		$publish = array();
		if($this->is_allowed(ADD_RIGHT))
		{
			$publish = array(
			'href' => $this->get_url(array(ExerciseTool :: PARAM_ACTION => ExerciseTool :: ACTION_PUBLISH)),
			'label' => Translation :: get('Publish'),
			'img' => Theme :: get_common_img_path().'action_publish.png'
			);
			//$publish = '<a href="' . $this->get_url(array(ExerciseTool :: PARAM_ACTION => ExerciseTool :: ACTION_PUBLISH), true) . '"><img src="'.Theme :: get_common_img_path().'action_publish.png" alt="'.Translation :: get('Publish').'" style="vertical-align:middle;"/> '.Translation :: get('Publish').'</a>';
		}
		$browse = array(
		'href' => $this->get_url(array(ExerciseTool :: PARAM_ACTION => ExerciseTool :: ACTION_VIEW_EXERCISES)),
		'label' => Translation :: get('BrowserTitle'),
		'img' => Theme :: get_common_img_path().'action_browser.png'
		);
		//'<a href="' . $this->get_url(array(ExerciseTool :: PARAM_ACTION => ExerciseTool :: ACTION_VIEW_EXERCISES), true) . '"><img src="'.Theme :: get_common_img_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a>';
		
		return array($browse,$publish);
	}*/
	
	/**
	 * Returns the actions that can be used on the middle part of the action bar in exercise components.
	 *
	 * @return array: actions to be used in middle part of the action bar.
	 */
	/*function get_right_actions()
	{
		return array();
	}*/
}

?>