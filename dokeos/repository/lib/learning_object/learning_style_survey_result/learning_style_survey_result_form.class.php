<?php

require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
require_once dirname(__FILE__) . '/learning_style_survey_result.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyResultForm extends LearningObjectForm
{
	const PARAM_SECTION = 'section';
	const PARAM_QUESTION = 'question';
	const PARAM_ANSWER = 'answer';
	
	private $profile;
	
	private $survey;
	
	function __construct($profile, $form_name, $method = 'post', $action = null)
	{
		$this->set_survey_profile($profile);
		$object = new AbstractLearningObject('learning_style_survey_result', api_get_user_id());
		parent :: __construct(self :: TYPE_CREATE, $object, $form_name, $method, $action);
	}
	
	function get_survey_profile()
	{
		return $this->profile;
	}
	
	function set_survey_profile($profile)
	{
		$this->profile = $profile;
	}
	
	function build_creation_form()
	{
		// TODO: find out why all this gets added _below_ the footer
		$this->answer_elements = array();
		// TODO: move & make customizable
		$pa_answers = array(
			1 => get_lang('LearningStyleSurveyStronglyDisagree'),
			2 => get_lang('LearningStyleSurveyDisagree'),
			3 => get_lang('LearningStyleSurveyNeutral'),
			4 => get_lang('LearningStyleSurveyAgree'),
			5 => get_lang('LearningStyleSurveyStronglyAgree')
		);
		$profile = $this->get_survey_profile();
		$this->survey = $profile->get_survey();
		$categories = $this->survey->get_survey_categories();
		$sections = $this->survey->get_survey_sections();
		$section_index = intval($_REQUEST[self :: PARAM_SECTION]);
		if ($section_index > count($sections) || $section_index < 0)
		{
			$section_index = 0;
		}
		if ($section_index > 0)
		{
			foreach (range(0, $section_index - 1) as $index)
			{
				$section = $sections[$index];
				$questions = $section->get_section_questions();
				foreach ($questions as $question_index => $question)
				{
					$name = self :: PARAM_ANSWER . $index . '_' . $question_index;
					$this->answer_elements[$index][$question_index] = $this->addElement('hidden', $name, $_POST[$name]);
				}
			}
		}
		// If not at end
		if ($section_index < count($sections))
		{
			$section = $sections[$section_index];
			$questions = $section->get_section_questions();
			$question_index = intval($_REQUEST[self :: PARAM_QUESTION]);
			if ($question_index >= count($questions) || $question_index < 0)
			{
				$question_index = 0;
			}
			if ($question_index > 0)
			{
				foreach (range(0, $question_index - 1) as $index)
				{
					$name = self :: PARAM_ANSWER . $section_index . '_' . $index;
					$this->answer_elements[$section_index][$question_index] = $this->addElement('hidden', $name, $_REQUEST[$name]);
				}
			}
			if ($question_index == 0)
			{
				$this->addElement('html', $section->get_description());
			}
			$question = $questions[$question_index];
			$this->addElement('html', $question->get_description());
			$name = self :: PARAM_ANSWER . $section_index . '_' . $question_index;
			if ($this->survey->get_survey_type() == LearningStyleSurvey :: SURVEY_TYPE_PROPOSITION_AGREEMENT)
			{
				$this->answer_elements[$section_index][$question_index] = $this->add_select($name, get_lang('YourAnswer'), $pa_answers, true);
			}
			elseif ($this->survey->get_survey_type() == LearningStyleSurvey :: SURVEY_TYPE_ANSWER_ORDERING)
			{
				$answers = $question->get_question_answers();
				$options = array();
				foreach ($answers as $answer_index => $answer)
				{
					$options[$answer_index] = $answer->get_description();
				}
				$this->answer_elements[$section_index][$question_index] = $this->addElement('option_orderer', $name, get_lang('YourAnswer'), $options);
				$this->addRule($name, get_lang('ThisFieldIsRequired'), 'required');
			}
			// Next question
			if ($question_index == count($questions) - 1)
			{
				$next_section = $section_index + 1;
				$next_question = 0;
			}
			else
			{
				$next_section = $section_index;
				$next_question = $question_index + 1;
			}
			$this->addElement('html', '<input type="hidden" name="' . self :: PARAM_SECTION . '" value="' . $next_section . '"/>');
			$this->addElement('html', '<input type="hidden" name="' . self :: PARAM_QUESTION . '" value="' . $next_question . '"/>');
		}
	}
	
	function build_editing_form()
	{
		// Not allowed
	}
	
	// Inherited
	function create_learning_object()
	{
		// TODO: save results
		$object = new LearningStyleSurveyResult();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	
	function setDefaults($defaults = array())
	{
		if ($this->survey->get_survey_type() == LearningStyleSurvey :: SURVEY_TYPE_PROPOSITION_AGREEMENT)
		{
			foreach ($this->answer_elements as $section => & $data)
			{
				foreach ($data as $question => $elmt)
				{
					// TODO: Don't hardcode (3 is the equivalent of "Neutral")
					$defaults[$elmt->getName()] = 3;
				}
			}
		}
		parent :: setDefaults($defaults);
	}
}

?>