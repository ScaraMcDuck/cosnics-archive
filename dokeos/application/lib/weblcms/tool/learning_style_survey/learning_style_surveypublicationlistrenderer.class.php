<?php
require_once dirname(__FILE__).'/../../browser/list_renderer/listlearningobjectpublicationlistrenderer.class.php';
/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyPublicationListRenderer extends ListLearningObjectPublicationListRenderer
{
	function render_title($publication)
	{
		$title = parent :: render_title($publication);
		$lo = $publication->get_learning_object();
		$html = '<a href="'
			. $this->get_url(array(LearningStyleSurveyTool :: PARAM_SURVEY_PROFILE_ID => $lo->get_id()), true)
			. '">' . $title . '</a>';
		if ($this->browser->get_parent()->is_allowed(ADD_RIGHT))
		{
			// TODO: better icon
			$html .= ' <a href="'
				. $this->get_url(array(LearningStyleSurveyTool :: PARAM_SURVEY_PROFILE_ID => $lo->get_id(), LearningStyleSurveyTool :: PARAM_VIEW_SURVEY_RESULTS => 1), true)
				. '"><img src="'.Theme :: get_common_img_path().'statistics.png" style="vertical-align: middle;" alt="'.htmlspecialchars('ViewSurveyResults').'"/></a>';
		}
		return $html;
	}
}
?>