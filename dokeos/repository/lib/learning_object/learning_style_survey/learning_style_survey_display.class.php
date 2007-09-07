<?php

require_once dirname(__FILE__) . '/../../learningobjectdisplay.class.php';
require_once dirname(__FILE__) . '/../../../../common/condition/equalitycondition.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$html = parent :: get_full_html();
		$survey = $this->get_learning_object();
		$categories = $survey->get_survey_categories();
		$sections = $survey->get_survey_sections();
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
			$category_map[$category->get_id()] = htmlspecialchars($category->get_title());
		}
		$html .= '</ul>';
		$html .= '<ol class="learning-style-survey-tree">';
		foreach ($sections as $section) {
			$html .= '<li>'
				. '<div class="learning-style-survey-section-title">'
				. htmlspecialchars($section->get_title())
				. '</div>'
				. '<ol>';
			$questions = $section->get_section_questions();
			foreach ($questions as $question) {
				$html .= '<li>'
					. '<div class="learning-style-survey-question-text">'
					. $question->get_description()
					. '</div>';
				if ($survey->get_survey_type() == LearningStyleSurvey :: SURVEY_TYPE_PROPOSITION_AGREEMENT)
				{
					$html .= '<div class="learning-style-survey-question-category">'
						. $category_map[$question->get_question_category_id()]
						. '</div>';
				}
				elseif ($survey->get_survey_type() == LearningStyleSurvey :: SURVEY_TYPE_ANSWER_ORDERING)
				{
					$html .= '<ul class="learning-style-survey-answers">';
					$answers = $question->get_question_answers();
					foreach ($answers as $answer)
					{
						$html .= '<li>'
							. '<div class="learning-style-survey-answer-text">'
							. $answer->get_description()
							. '</div>'
							. '<div class="learning-style-survey-answer-category">'
							. $category_map[$answer->get_answer_category_id()]
							. '</div>'
							. '</li>';
					}
					$html .= '</ul>';
				}
				$html .= '</li>';
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