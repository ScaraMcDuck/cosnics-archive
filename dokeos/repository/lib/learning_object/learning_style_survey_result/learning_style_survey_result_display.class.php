<?php

require_once dirname(__FILE__) . '/../../learning_object_display.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyResultDisplay extends LearningObjectDisplay
{
	private $admin;
	
	function set_administrative_view($admin)
	{
		$this->admin = $admin;
	}
	
	function get_full_html()
	{
		$result = $this->get_learning_object();
		$profile = $result->get_profile();
		$survey = $profile->get_survey();
		// TODO: display survey title
		$answers = $result->get_result_answers();
		$answer_data = array();
		foreach ($answers as $answer)
		{
			$question = $answer->get_question();
			$answer_data[$question->get_id()] = $answer->get_answer(); 
		}
		$survey = $profile->get_survey();
		$category_total = $this->calculate_results($profile, $answer_data);
		// TODO: determine how much to display in each case
		if ($this->admin)
		{
			$user_id = $result->get_owner_id();
			$user = UserManager :: retrieve_user($user_id);
			$user = ($user ? $user->get_fullname() : 'User #' . $user_id);
			return $this->format_result($profile, $category_total, $user)
				. $this->format_answers($profile, $answer_data, $user);
		}
		else
		{
			return $this->format_result($profile, $category_total)
				. $this->format_answers($profile, $answer_data);
		}
	}

	private function format_answers($profile, $answer_data, $user = null)
	{
		$survey = $profile->get_survey();
		$sections = $survey->get_survey_sections();
		$model = $survey->get_survey_model();
		$title = (isset($user)
			? Translation :: get('SurveyAnswersOfUserPrefix') . ' ' . $user
			: Translation :: get('MySurveyAnswers'));
		$answers_html = '<div class="survey-result-header">' . htmlspecialchars($title) . '</div>';
		$answers_html .= '<ol class="survey-user-answers">';
		foreach ($sections as $section)
		{
			$answers_html .= '<li class="survey-user-answers-section">' . $section->get_description() . '<ol>';
			$questions = $section->get_section_questions();
			foreach ($questions as $question)
			{
				$answers_html .= '<li class="survey-user-answer-container">'
					. '<div class="survey-user-answer-question">' . $question->get_description() . '</div>'
					. '<div class="survey-user-answer">' . $model->format_answer($answer_data, $profile, $section, $question) . '</div>'
					. '</li>';
			}
			$answers_html .= '</ol></li>';
		}
		$answers_html .= '</ol>';
		return $answers_html;
	}
	
	private function format_result($profile, $category_total, $user = null)
	{
		$survey = $profile->get_survey();
		$model = $survey->get_survey_model();
		$titles = array();
		$data = array();
		$title = (isset($user)
			? Translation :: get('SurveyResultsOfUserPrefix') . ' ' . $user
			: Translation :: get('MySurveyResults'));
		$result_html = '<div class="survey-result-header">' . htmlspecialchars($title) . '</div>';
		$result_html .= '<dl class="survey-user-results">';
		$categories = $survey->get_survey_categories();
		foreach ($categories as $category)
		{
			$num = $category_total[$category->get_id()];
			$result_html .= '<dt class="survey-category-title">' . htmlspecialchars($category->get_title()) . '</dt>'
				. '<dd class="survey-category-description">' . $category->get_description() . '</dd>'
				. '<dd class="survey-user-result">' . $num . '</dd>';
			$titles[] = $category->get_title();
			$data[] = $num / $model->get_maximum_category_score($profile, $category) * 100;
		}
		$result_html .= '</dl>';
		if (count($data) > 2)
		{
			require_once dirname(__FILE__).'/inc/PsychePolygon.class.php';
			$p = new PsychePolygon($titles, $data);
			$img = $p->create_image(PsychePolygon::IMAGE_TYPE_PNG);
			$result_html .= '<div class="survey-user-result-polygon"><img src="data:' . $img['mime_type'] . ';base64,'
				. base64_encode($img['data']) . '"'
				. ' width="' . $img['width'] . '" height="' . $img['height'] . '"/></div>';
		}
		$result_html .= $survey->get_survey_model()->get_additional_result_html(
			$profile, $category_total);
		return $result_html;
	}

	private function calculate_results ($profile, $answer_data)
	{
		$survey = $profile->get_survey();
		$model = $survey->get_survey_model();
		$sections = $survey->get_survey_sections();
		$res = array();
		foreach ($sections as $section)
		{
			$questions = $section->get_section_questions();
			foreach ($questions as $question)
			{
				$model->calculate_result($res, $answer_data, $profile, $section, $question);
			}
		}
		return $res;
	}
}

?>