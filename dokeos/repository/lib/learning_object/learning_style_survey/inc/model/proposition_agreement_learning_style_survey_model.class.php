<?php

require_once dirname(__FILE__) . '/../learning_style_survey_model.class.php';

/**
 * @author Tim De Pauw
 */
class PropositionAgreementLearningStyleSurveyModel extends LearningStyleSurveyModel
{
	function calculate_result(& $result, & $answer_data, $survey, $section, $question)
	{
		$result[$question->get_question_category_id()] += $answer_data[$question->get_id()];
	}
	
	function format_answer(& $answer_data, $survey, $section, $question)
	{
		$answer = $answer_data[$question->get_id()];
		$pa_answers = self :: get_possible_answers();
		return '<p>' . htmlspecialchars($pa_answers[$answer]) . '</p>';
	}
	
	function format_question($survey, $section, $question, & $categories)
	{
		return '<div class="learning-style-survey-question-text">'
			. $question->get_description()
			. '</div>'
			. '<div class="learning-style-survey-question-category">'
			. $this->format_category_name($question->get_question_category_id(), $categories)
			. '</div>';
	}
	
	function create_user_answer_element($name, $survey, $section, $question)
	{
		$pa_answers = self :: get_possible_answers();
		$element = new HTML_QuickForm_select($name, get_lang('YourAnswer'), $pa_answers);
		$keys = array_keys($pa_answers);
		sort($keys, SORT_NUMERIC);
		return array(
			'element' => $element,
			'default' => $keys[floor(count($keys) / 2)]
		);
	}
	
	function save_user_answer($survey, $section, $question, $answer_element, $owner_id, $parent_object_id)
	{
		$answer = new LearningStyleSurveyUserAnswer();
		$answer->set_owner_id($owner_id);
		// TODO
		//$answer->set_title($something);
		$answer->set_parent_id($parent_object_id);
		$answer->set_question_id($question->get_id());
		$answer->set_answer($answer_element->exportValue());
		$answer->create();
	}
	
	function get_maximum_category_score($survey, $category)
	{
		// Number of questions in this category
		$num = 0;
		foreach ($survey->get_survey_sections() as $section)
		{
			foreach ($section->get_section_questions() as $question)
			{
				$cid = $question->get_question_category_id();
				if ($cid && $cid == $category->get_id())
				{
					$num++; 
				}
			}
		}
		$pa_answers = self :: get_possible_answers();
		// The number of available answers is also the maximum score per
		// question; hence, the product is the maximum category score
		return $num * count($pa_answers);
	}
	
	function get_parameter_names()
	{
		return array(
			'first_quartile_end',
			'second_quartile_end',
			'third_quartile_end'
		);
	}
		
	private static function get_possible_answers()
	{
		// TODO: Make customizable
		return array(
			1 => get_lang('LearningStyleSurveyStronglyDisagree'),
			2 => get_lang('LearningStyleSurveyDisagree'),
			3 => get_lang('LearningStyleSurveyNeutral'),
			4 => get_lang('LearningStyleSurveyAgree'),
			5 => get_lang('LearningStyleSurveyStronglyAgree')
		);
	}
}

?>