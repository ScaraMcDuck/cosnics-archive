<?php

require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
require_once dirname(__FILE__) . '/learning_style_survey.class.php';
require_once dirname(__FILE__) . '/../learning_style_survey_answer/learning_style_survey_answer.class.php';
require_once dirname(__FILE__) . '/../learning_style_survey_category/learning_style_survey_category.class.php';
require_once dirname(__FILE__) . '/../learning_style_survey_question/learning_style_survey_question.class.php';
require_once dirname(__FILE__) . '/../learning_style_survey_section/learning_style_survey_section.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyForm extends LearningObjectForm
{
	const PARAM_SURVEY_TYPE = 'lss_type';
	const PARAM_SECTION_COUNT = 'lss_section_count';
	const PARAM_CATEGORY_COUNT = 'lss_category_count';
	const PARAM_CATEGORY_NAME = 'lss_category_name';
	const PARAM_CATEGORY_DESCRIPTION = 'lss_category_description';
	const PARAM_SECTION_TITLE = 'lss_section_name';
	const PARAM_SECTION_INTRODUCTION = 'lss_section_introduction';
	const PARAM_QUESTION_COUNT = 'lss_question_count';
	const PARAM_QUESTION = 'lss_question';
	const PARAM_QUESTION_CATEGORY = 'lss_question_category';
	const PARAM_ANSWER_COUNT = 'lss_answer_count';
	const PARAM_ANSWER = 'lss_answer';
	const PARAM_ANSWER_CATEGORY = 'lss_answer_category';
		
	private $type_element;
	
	private $category_count_element;
	
	private $section_count_element;
	
	private $category_elements;
	
	private $section_elements;
	
	private $question_count_elements;
	
	private $question_elements;
	
	private $answer_elements;
			
	protected function build_creation_form()
	{
		// TODO: Add hidden |step| element; override error reporting when appropriate
		// TODO: Some sensible defaults based on the type, especially for count fields
		// TODO: Extract methods
		parent::build_creation_form();
		$this->type_element = $this->add_select(
			LearningStyleSurvey :: PROPERTY_SURVEY_TYPE,
			get_lang('SurveyType'),
			LearningStyleSurvey::get_available_survey_types()
		);
		// Entered default survey properties?
		if ($this->validate())
		{
			$this->type_element->freeze();
			$this->category_count_element = $this->add_textfield(self :: PARAM_CATEGORY_COUNT, get_lang('SurveyCategoryCount'));
			$this->section_count_element = $this->add_textfield(self :: PARAM_SECTION_COUNT, get_lang('SurveySectionCount'));
			// Entered category count and section count?
			if ($this->validate())
			{
				$this->category_count_element->freeze();
				$this->section_count_element->freeze();
				$category_count = intval($this->category_count_element->exportValue());
				$section_count = intval($this->section_count_element->exportValue());
				$this->category_elements = array();
				foreach (range(1, $category_count) as $i) {
					$name = self :: PARAM_CATEGORY_NAME . $i;
					$name_el = $this->add_textfield($name, get_lang('SurveyCategoryName') . ' ' . $i);
					$desc_el = $this->add_html_editor(self :: PARAM_CATEGORY_DESCRIPTION . $i, get_lang('SurveyCategoryDescription') . ' ' . $i);
					$this->category_elements[$i] = array(
						'name' => $name_el,
						'description' => $desc_el
					);
				}
				$this->section_elements = array();
				foreach (range(1, $section_count) as $i) {
					$name = self :: PARAM_SECTION_TITLE . $i;
					$title_el = $this->add_textfield($name, get_lang('SurveySectionTitle') . ' ' . $i);
					$intro_el = $this->add_html_editor(self :: PARAM_SECTION_INTRODUCTION . $i, get_lang('SurveySectionIntroduction') . ' ' . $i);
					$this->section_elements[$i] = array(
						'title' => $title_el,
						'introduction' => $intro_el
					);
				}
				// Entered categories and sections?
				if ($this->validate())
				{
					foreach ($this->category_elements as & $els)
					{
						foreach ($els as $id => $el)
						{
							$el->freeze();
						}
					}
					$this->question_count_elements = array();
					foreach ($this->section_elements as $section => & $els)
					{
						foreach ($els as $id => $el)
						{
							$el->freeze();
						}
						$name = self :: PARAM_QUESTION_COUNT . $section;
						$this->question_count_elements[$section] = $this->add_textfield($name, get_lang('SurveySectionQuestionCount') . ' ' . $section);
					}
					// Entered question counts?
					if ($this->validate())
					{
						$this->question_elements = array();
						$this->answer_elements = array();
						$survey_type = $this->get_survey_type();
						foreach ($this->question_count_elements as $elem)
						{
							$elem->freeze();
						}
						$categories = array();
						$categories[0] = get_lang('None');
						foreach ($this->category_elements as $category => & $els)
						{
							$categories[$category] = $els['name']->exportValue();
						}
						foreach ($this->section_elements as $section => & $els)
						{
							foreach (range(1, intval($this->question_count_elements[$section]->exportValue())) as $question)
							{
								$name = self :: PARAM_QUESTION . $section . '_' . $question;
								$this->question_elements[$section][$question]['text'] = $this->add_html_editor($name, get_lang('SurveySectionQuestion') . ' ' . $section . '.' . $question);
								if ($survey_type == LearningStyleSurvey :: SURVEY_TYPE_PROPOSITION_AGREEMENT)
								{
									$name = self :: PARAM_QUESTION_CATEGORY . $section . '_' . $question;
									$this->question_elements[$section][$question]['category'] = $this->add_select($name, get_lang('SurveySectionQuestionCategory') . ' ' . $section . '.' . $question, $categories);
								}
								elseif ($survey_type == LearningStyleSurvey :: SURVEY_TYPE_ANSWER_ORDERING)
								{
									$name = self :: PARAM_ANSWER_COUNT . $section . '_' . $question;
									$this->question_elements[$section][$question]['answers'] = $this->add_textfield($name, get_lang('SurveySectionQuestionAnswerCount') . ' ' . $section . '.' . $question);
								}
								else
								{
									// Won't happen, but hey.
									die('Invalid survey type');
								}
							}
						}
						// Entered answer counts? (if relevant)
						if ($survey_type == LearningStyleSurvey :: SURVEY_TYPE_ANSWER_ORDERING && $this->validate())
						{
							$this->answer_elements = array();
							foreach ($this->section_elements as $section => & $els)
							{
								foreach ($this->question_elements[$section] as $question => & $data)
								{
									$data['text']->freeze();
									$answer_count = $data['answers'];
									$answer_count->freeze();
									foreach (range(1, intval($answer_count->exportValue())) as $answer)
									{
										$name = self :: PARAM_ANSWER . $section . '_' . $question . '_' . $answer;
										$this->answer_elements[$section][$question][$answer]['text'] = $this->add_html_editor($name, get_lang('SurveySectionQuestionAnswer') . ' ' . $section . '.' . $question . '.' . $answer);
										$name = self :: PARAM_ANSWER_CATEGORY . $section . '_' . $question . '_' . $answer;
										$this->answer_elements[$section][$question][$answer]['category'] = $this->add_select($name, get_lang('SurveySectionAnswerCategory') . ' ' . $section . '.' . $question . '.' . $answer, $categories);
									}
								}
							}
						}
					}
				}
			}
		}
	} // Everybody loves curly braces.
	
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		// TODO
	}
	
	function create_learning_object()
	{
		$survey_type = $this->get_survey_type();
		// The object itself
		$survey = new LearningStyleSurvey();
		$survey->set_survey_type($survey_type);
		$this->set_learning_object($survey);
		$return_value = parent :: create_learning_object();
		// Categories
		$categories = array();
		// For "none"; not really necessary here, it's null anyway
		$categories[0] = null;
		foreach ($this->category_elements as $cid => & $els)
		{
			$name = $els['name']->exportValue();
			$description = $els['description']->exportValue();
			$category = new LearningStyleSurveyCategory(); 
			$category->set_owner_id($this->get_owner_id());
			$category->set_title($name);
			$category->set_description($description);
			$category->set_parent_id($survey->get_id());
			$category->create();
			$categories[$cid] = $category->get_id();
		}
		// Sections
		foreach ($this->section_elements as $sid => & $els)
		{
			$name = $els['title']->exportValue();
			$description = $els['introduction']->exportValue();
			$section = new LearningStyleSurveySection(); 
			$section->set_owner_id($this->get_owner_id());
			$section->set_title($name);
			$section->set_description($description);
			$section->set_parent_id($survey->get_id());
			$section->create();
			// Questions in section
			foreach ($this->question_elements[$sid] as $qid => & $data)
			{
				$question_text = $data['text']->exportValue();
				$question = new LearningStyleSurveyQuestion();
				$question->set_owner_id($this->get_owner_id());
				$question->set_title($survey->get_title() . ' Q#' . $sid . '.' . $qid);
				$question->set_description($question_text);
				$question->set_parent_id($section->get_id());
				if ($survey_type == LearningStyleSurvey :: SURVEY_TYPE_PROPOSITION_AGREEMENT)
				{
					$question->set_question_category_id($categories[$data['category']->exportValue()]);
				}
				$question->create();
				if ($survey_type == LearningStyleSurvey :: SURVEY_TYPE_ANSWER_ORDERING)
				{
					// Answers to question
					foreach ($this->answer_elements[$sid][$qid] as $aid => & $adata) {
						$answer_text = $adata['text']->exportValue();
						$answer = new LearningStyleSurveyAnswer();
						$answer->set_owner_id($this->get_owner_id());
						$answer->set_title($survey->get_title() . ' A#' . $sid . '.' . $qid . '.' . $aid);
						$answer->set_description($answer_text);
						$answer->set_parent_id($question->get_id());
						$answer->set_answer_category_id($categories[$adata['category']->exportValue()]);
						$answer->create();
					}
				}
			}
		}
		return $return_value;
	}
	
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		// TODO
		return parent :: update_learning_object();
	}
	
	private function get_survey_type() {
		return $this->type_element->exportValue();
	}
}

?>