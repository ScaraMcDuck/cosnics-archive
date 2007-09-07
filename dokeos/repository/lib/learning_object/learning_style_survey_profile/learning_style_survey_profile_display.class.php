<?php

require_once dirname(__FILE__) . '/../../learningobjectdisplay.class.php';
require_once dirname(__FILE__) . '/../learning_style_survey/learning_style_survey_display.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyProfileDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$lo = $this->get_learning_object();
		$disp = new LearningStyleSurveyDisplay($lo->get_survey());
		$md = $lo->get_profile_metadata();
		$html = $disp->get_full_html();
		if (is_array($md))
		{
			$html .= '<dl class="learning-style-survey-metadata">';
			foreach ($md as $key => $value)
			{
				$html .= '<dt>' . htmlspecialchars($key) . '</dt>'
					. '<dd>' . nl2br(htmlspecialchars($value)) . '</dd>';
			}
			$html .= '</dl>';
		}
		return $html;
	}
}

?>