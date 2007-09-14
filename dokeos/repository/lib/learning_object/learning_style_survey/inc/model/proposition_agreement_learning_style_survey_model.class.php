<?php

require_once dirname(__FILE__) . '/../learning_style_survey_model.class.php';

/**
 * @author Tim De Pauw
 */
class PropositionAgreementLearningStyleSurveyModel extends LearningStyleSurveyModel
{
	function calculate_result(& $result, & $answer_data, $profile, $section, $question)
	{
		foreach ($question->get_question_category_ids() as $cid)
		{
			$result[$cid] += $answer_data[$question->get_id()];
		}
	}
	
	function format_answer(& $answer_data, $survey, $section, $question)
	{
		$answer = $answer_data[$question->get_id()];
		$pa_answers = self :: get_possible_answers($profile);
		return '<p>' . htmlspecialchars($pa_answers[$answer]) . '</p>';
	}
	
	function format_question($survey, $section, $question, & $categories)
	{
		$html = '<div class="learning-style-survey-question-text">'
			. $question->get_description()
			. '</div>';
		$cids = $question->get_question_category_ids();
		if (count($cids))
		{
			$html .= '<ul class="learning-style-survey-question-categories">';
			foreach ($cids as $cid)
			{
				$html .= '<li>' . $this->format_category_name($cid, $categories) . '</li>';
			}
			$html .= '</ul>';
		}
		else
		{
			$html .= '<div class="learning-style-survey-question-no-categories">'
				. get_lang('NoSurveyQuestionCategories')
				. '</div>';
		}
		return $html;
	}
	
	function create_user_answer_element($name, $profile, $section, $question)
	{
		$pa_answers = self :: get_possible_answers($profile);
		$element = new HTML_QuickForm_select($name, get_lang('YourAnswer'), $pa_answers);
		$keys = array_keys($pa_answers);
		sort($keys, SORT_NUMERIC);
		return array(
			'element' => $element,
			'default' => $keys[floor(count($keys) / 2)]
		);
	}
	
	function save_user_answer($profile, $section, $question, $answer_element, $owner_id, $parent_object_id)
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
	
	function get_maximum_category_score($profile, $category)
	{
		// Number of questions in this category
		$num = 0;
		foreach ($profile->get_survey()->get_survey_sections() as $section)
		{
			foreach ($section->get_section_questions() as $question)
			{
				foreach ($question->get_question_category_ids() as $cid)
				{
					if ($cid == $category->get_id())
					{
						$num++; 
					}
				}
			}
		}
		$pa_answers = self :: get_possible_answers($profile);
		// The number of available answers is also the maximum score per
		// question; hence, the product is the maximum category score
		return $num * count($pa_answers);
	}
	
	function get_additional_parameters()
	{
		// TODO: This is specific to the Pointcarré implementation of the
		// Vermunt test. We need a way to make this stuff generic without
		// breaking usability too much. Input validation would be nice.
		return array(
			'Percentiles' => 'Enter percentile limits separated by spaces, one line per category. Omit 0 and 100. Use the same order for categories as you did when adding the survey.' . "\n"
				. 'Example for 2 categories, each divided into 5 percentiles:' . "\n"
				. '29 34 40 45' . "\n"
				. '26 31 37 41',
			'AnswerCount' => 'Enter the number of possible answers per question. Defaults to 5.'
		);
	}
	
	function get_additional_result_html ($profile, & $result, & $answer_data)
	{
		$metadata = $profile->get_profile_metadata();
		$lines = preg_split('/(\r\n|\n|\r)/', $metadata['Percentiles']);
		$survey = $profile->get_survey();
		$categories = $survey->get_survey_categories();
		$percentiles = array();
		foreach ($lines as $index => $line)
		{
			preg_match_all('/\S+/', $line, $matches);
			$percentiles[] = array_map('floatval', $matches[0]);
		}
		$html = '<h4>' . get_lang('SurveyResultPeerComparisonTitle') . '</h4>';
		$html .= '<dl class="survey-result-peer-comparison">';
		foreach ($categories as $index => $category)
		{
			$p = $percentiles[$index];
			$i = 0;
			while ($i < count($p) && $result[$category->get_id()] > $p[$i])
			{
				$i++;
			}
			$percentile = $i + 1;
			$delta = 100 / (count($p) + 1);
			$range = round(($percentile - 1) * $delta)
				. '&ndash;' . round($percentile * $delta) . '%';
			$html .= '<dt>' . htmlspecialchars($category->get_title()) . '</dt>'
				. '<dd>' . $range . '</dd>';
		}
		$html .= '</dl>';
		return $html;
	}
	
	private static function get_possible_answers($profile = null)
	{
		if (!$profile)
		{
			$answer_count = 5;
		}
		else
		{
			$metadata = $profile->get_profile_metadata();
			$answer_count = intval($metadata['AnswerCount']);
			if ($answer_count < 2)
			{
				$answer_count = 5;
			}
		}
		$answers = array();
		foreach (range(1, $answer_count) as $i)
		{
			$answer = get_lang('LearningStyleSurveyAgreement_' . $answer_count . '_' . $i);
			$answers[] = (substr($answer, 0, 2) != '[='
				? $answer
				: str_replace(
					'%percentage%',
					round(100 * ($i - 1) / ($answer_count - 1)),
					get_lang('IAgreePercentage')
				)
			);
		}
		return $answers;
	}
}

?>