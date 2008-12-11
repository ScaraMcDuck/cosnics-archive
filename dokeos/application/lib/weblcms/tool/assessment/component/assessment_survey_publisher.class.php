<?php
require_once dirname (__FILE__).'/assessment_survey_publisher/survey_publisher_component.class.php';

class AssessmentToolSurveyPublisherComponent extends AssessmentToolComponent
{
	function run()
	{
		
		$type = $_GET[AssessmentTool :: PARAM_PUBLICATION_ACTION];
		$publisher_component = SurveyPublisherComponent :: factory($this, $type);
		
		$publisher_component->run();
	}
	
	function get_toolbar()
	{
		$toolbar = parent :: get_toolbar();
		
		if ($this->is_allowed(EDIT_RIGHT))
		{
			$toolbar->add_tool_action(
				new ToolbarItem(
					Translation :: get('ViewInvitedUsers'), Theme :: get_common_image_path().'action_visible.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH_SURVEY, AssessmentTool :: PARAM_PUBLICATION_ACTION => AssessmentTool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => $_GET[Tool :: PARAM_PUBLICATION_ID])), ToolbarItem :: DISPLAY_ICON_AND_LABEL
				)
			);
			
			$toolbar->add_tool_action(
				new ToolbarItem(
					Translation :: get('PublishSurvey'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH_SURVEY, Tool :: PARAM_PUBLICATION_ID => $_GET[Tool :: PARAM_PUBLICATION_ID])), ToolbarItem :: DISPLAY_ICON_AND_LABEL
				)
			);
		}
		
		return $toolbar;
	}
}
?>