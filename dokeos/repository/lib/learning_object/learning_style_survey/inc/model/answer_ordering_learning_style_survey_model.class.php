<?php

require_once dirname(__FILE__) . '/../learning_style_survey_model.class.php';
require_once Path :: get_library_path().'html/formvalidator/Element/option_orderer.php';

/**
 * @author Tim De Pauw
 */
class AnswerOrderingLearningStyleSurveyModel extends LearningStyleSurveyModel
{
	const SEPARATOR = '|';
	
	function calculate_result($result, $answer_data, $profile, $section, $question)
	{
		$answers = $question->get_question_answers();
		foreach ($answers as $answer)
		{
			foreach ($answer->get_answer_category_ids() as $cid)
			{
				$result[$cid] += $answer_data[$answer->get_id()];
			}
		}
	}
	
	function format_answer($answer_data, $profile, $section, $question)
	{
		$answers = $question->get_question_answers();
		$order = array();
		foreach ($answers as $answer)
		{
			$pos = $answer_data[$answer->get_id()];
			$order[$pos - 1] = $answer->get_description();
		}
		$answers_html = '<ol>';
		for ($i = 0; $i < count($order); $i++)
		{
			$answers_html .= '<li>' . $order[$i] . '</li>';
		}
		$answers_html .= '</ol>';
		return $answers_html;
	}
	
	function format_question($survey, $section, $question, $categories)
	{
		$html = '<div class="learning-style-survey-question-text">'
			. $question->get_description()
			. '</div>'
			. '<ul class="learning-style-survey-answers">';
		$answers = $question->get_question_answers();
		foreach ($answers as $answer)
		{
			$cids = $answer->get_answer_category_ids();
			$html .= '<li>'
				. '<div class="learning-style-survey-answer-text">'
				. $answer->get_description()
				. '</div>';
			if (count($cids))
			{
				$html .= '<ul class="learning-style-survey-answer-categories">';
				foreach ($cids as $cid)
				{
					$html .= '<li>' . $this->format_category_name($cid, $categories);
				}
				$html .= '</ul>';
			}
			else
			{
				$html .= '<div class="learning-style-survey-answer-no-categories">'
					. Translation :: get_lang('NoSurveyAnswerCategories')
					. '</div>';
			}
			$html .= '</li>';
		}
		$html .= '</ul>';
		return $html;
	}
	
	function create_user_answer_element($name, $profile, $section, $question)
	{
		$answers = $question->get_question_answers();
		$options = array();
		foreach ($answers as $answer_index => $answer)
		{
			$options[$answer->get_id()] = $answer->get_description();
		}
		$element = new HTML_QuickForm_option_orderer($name, Translation :: get_lang('YourAnswer'), $options, self :: SEPARATOR);
		return array(
			'element' => $element
		);
	}
	
	function save_user_answer($profile, $section, $question, $answer_element, $owner_id, $parent_object_id)
	{
		$answers = $question->get_question_answers();
		$valid_answer = array();
		foreach ($answers as $answers)
		{
			$valid_answer[$answers->get_id()] = true;
		}
		$user_answers = explode(self :: SEPARATOR, $answer_element->exportValue());
		foreach ($user_answers as $index => $answer_id)
		{
			if (!$valid_answer[$answer_id])
			{
				// The user probably tampered with POST data.
				// TODO: Handle more cleanly.
				die('Invalid answer');
			}
			$answer = new LearningStyleSurveyUserAnswer();
			$answer->set_owner_id($owner_id);
			// TODO
			//$answer->set_title($something);
			$answer->set_parent_id($parent_object_id);
			$answer->set_question_id($answer_id);
			$answer->set_answer($index + 1);
			$answer->create();
		}
	}
	
	function get_maximum_category_score($profile, $category)
	{
		$num = 0;
		foreach ($profile->get_survey()->get_survey_sections() as $section)
		{
			foreach ($section->get_section_questions() as $question)
			{
				$answers = $question->get_question_answers();
				$max_score = count($answers);
				foreach ($answers as $answer)
				{
					foreach ($answer->get_answer_category_ids() as $cid)
					{
						if ($cid == $category->get_id())
						{
							$num += $max_score;
						}
					}
				}
			}
		}
		return $num;
	}
	
	function get_additional_parameters()
	{
		return array();
	}
}

?>