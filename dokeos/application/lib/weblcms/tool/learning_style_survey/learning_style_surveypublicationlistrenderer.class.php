<?php
require_once dirname(__FILE__).'/../../browser/learningobjectpublicationlistrenderer.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyPublicationListRenderer extends LearningObjectPublicationListRenderer
{
	function __construct($browser)
	{
		parent :: __construct($browser);
	}
	
	function as_html()
	{
		$publications = $this->get_publications();
		$html = '<ul>';
		foreach ($publications as $publication)
		{
			$html .= $this->render_publication($publication);
		}
		$html .= '</ul>';
		return $html;
	}
	
	function render_publication($publication)
	{
		$lo = $publication->get_learning_object();
		$html = '<li>'
			. '<div class="learning-style-survey-profile-title">'
			. '<a href="'
			. htmlspecialchars(
				$this->browser->get_parent()->get_url(array(LearningStyleSurveyTool :: PARAM_SURVEY_PROFILE_ID => $lo->get_id()))
			)
			. '">' . htmlspecialchars($lo->get_title()) . '</a>';
		if ($this->browser->get_parent()->is_allowed(ADD_RIGHT))
		{
			$results_url = $this->browser->get_parent()->get_url(array(LearningStyleSurveyTool :: PARAM_SURVEY_PROFILE_ID => $lo->get_id(), LearningStyleSurveyTool :: PARAM_VIEW_SURVEY_RESULTS => 1));
			$html .= ' <a href="' . htmlspecialchars($results_url) . '">[' . get_lang('ViewResults') . ']</a>';
		}
		$html .= '</div>'
			. $lo->get_description()
			. '</li>';
		return $html;
	}
}
?>