<?php
/**
 * @package application.weblcms.tool.assessment
 */
require_once Path :: get_library_path().'html/action_bar/action_bar_renderer.class.php';
/**
 * The base class for all assessment tool components.
 *
 */
class AssessmentToolComponent extends ToolComponent 
{
	
	/**
	 * Inherited
	 *
	 * @param unknown_type $component_name
	 * @param unknown_type $assessment_tool
	 * @return unknown
	 */
	static function factory ($component_name, $assessment_tool) 
	{
		return parent :: factory('Assessment', $component_name, $assessment_tool);
	}
	
	function get_toolbar() 
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url());
		$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Publish'), Theme :: get_common_img_path().'action_publish.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
		
		$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Browse'), Theme :: get_common_img_path().'action_browser.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_ASSESSMENTS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
		
		if ($this->is_allowed(EDIT_RIGHT))
		{
			$action_name = Translation :: get('View results summary');
		}
		else {
			$action_name = Translation :: get('View results');
		}
		$action_bar->add_tool_action(
			new ToolbarItem(
				$action_name, Theme :: get_common_img_path().'action_view_results.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
		
		return $action_bar;
	}
	
}

?>