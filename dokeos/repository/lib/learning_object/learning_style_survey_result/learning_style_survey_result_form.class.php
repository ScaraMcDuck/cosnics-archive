<?php

require_once dirname(__FILE__) . '/../../learning_object_form.class.php';
require_once dirname(__FILE__) . '/learning_style_survey_result.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyResultForm extends LearningObjectForm
{
	const PARAM_SECTION = 'section';
	const PARAM_QUESTION = 'question';
	const PARAM_ANSWER = 'answer';
	
	const KEY_PROFILE = '__profile__';
		
	private $profile;
	
	private $survey;
	
	private $answer_elements;
	
	protected function __construct ($form_type, $learning_object, $form_name, $method = 'post', $action = null, $extra = null)
	{
		// Can't pass extra parameters, so we sneak the profile into $extra
		$this->set_survey_profile($extra[self :: KEY_PROFILE]);
		unset($extra[self :: KEY_PROFILE]);
		parent :: __construct($form_type, $learning_object, $form_name, $method, $action, $extra);
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
		$this->addElement('category', true, Translation :: get(get_class($this) .'Properties'));
		$this->answer_elements = array();
		$this->defaults = array();
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
			$model = $this->survey->get_survey_model();
			$name = self :: PARAM_ANSWER . $section_index . '_' . $question_index;
			$element_data = $model->create_user_answer_element($name, $profile, $section, $question);
			$element = $element_data['element'];
			if (array_key_exists('default', $element_data))
			{
				$this->defaults[$name] = $element_data['default'];
			}
			$this->addElement($element);
			$this->addRule($name, null, 'required');
			$this->answer_elements[$section_index][$question_index] = $element;
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
		// If the user did not answer a question properly, we just show it
		// again. If not, we go to the next one. This breaks input validation,
		// as QF thinks the form wasn't filled out properly. Luckily, we can
		// just handle this "error" silently.
		$this->set_error_reporting(false);
		$this->addElement('category');
	}
	
	function build_editing_form()
	{
		// Not allowed
	}
	
	// Inherited
	function create_learning_object()
	{
		$object = new LearningStyleSurveyResult();
		$this->set_learning_object($object);
		$object->set_owner_id($this->get_owner_id());
		$categories = array_keys($this->get_categories());
		// Add in root category
		$object->set_parent_id($categories[0]);
		$object->set_title(Translation :: get('SurveyResultPrefix') . ' ' . $this->survey->get_title());
		$object->set_profile_id($this->get_survey_profile()->get_id());
		// No result metadata necessary ...
		$object->create();
		$sections = $this->survey->get_survey_sections();
		$model = $this->survey->get_survey_model();
		foreach ($sections as $section_index => $section)
		{
			$questions = $section->get_section_questions();
			foreach ($questions as $question_index => $question)
			{
				$elmt = $this->answer_elements[$section_index][$question_index];
				$model->save_user_answer($this->profile, $section, $question, $elmt, $this->get_owner_id(), $object->get_id());
			}
		}
		return $object;
	}
	
	function setDefaults($defaults = array())
	{
		parent :: setDefaults(array_merge($defaults, $this->defaults));
	}
}

?>