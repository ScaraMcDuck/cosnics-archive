<?php

require_once dirname(__FILE__) . '/../../learning_object_display.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$html = parent :: get_full_html();
		$survey = $this->get_learning_object();
		$model = $survey->get_survey_model();
		$categories = $survey->get_survey_categories();
		$sections = $survey->get_survey_sections();
		$html .= '<div class="survey-categories-header">' . Translation :: get('LearningStyleSurveyCategoryList') . '</div>';
		$html .= '<ul class="learning-style-survey-categories">';
		$category_map = array();
		foreach ($categories as $category) {
			$html .= '<li>'
				. '<div class="learning-style-survey-category-title">'
				. htmlspecialchars($category->get_title())
				. '</div>'
				. '<div class="learning-style-survey-category-description">'
				. $category->get_description()
				. '</div>'
				. '</li>';
			$category_map[$category->get_id()] = $category;
		}
		$html .= '</ul>';
		$html .= '<div class="survey-outline-header">' . Translation :: get('LearningStyleSurveyOutline') . '</div>';
		$html .= '<ol class="learning-style-survey-tree">';
		foreach ($sections as $section) {
			$html .= '<li class="learning-style-survey-section">'
				. '<div class="learning-style-survey-section-title">'
				. htmlspecialchars($section->get_title())
				. '</div>'
				. '<ol class="learning-style-survey-section-questions">';
			$questions = $section->get_section_questions();
			foreach ($questions as $question) {
				$html .= '<li class="learning-style-survey-section-question">'
					. $model->format_question($survey, $section, $question, $category_map)
					. '</li>';
			}
			$html .= '</ol>';
			$html .= '</li>';
		}
		$html .= '</ol>';
		return $html;
	}
	
	/*
	function get_short_html()
	{
	}
	*/
}

?>