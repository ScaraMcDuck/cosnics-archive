<?php

require_once dirname(__FILE__) . '/../../learning_object_form.class.php';
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
	const PARAM_STEP = 'lss_step';
	
	private $type_element;
	
	private $category_count_element;
	
	private $section_count_element;
	
	private $category_elements;
	
	private $section_elements;
	
	private $question_count_elements;
	
	private $question_elements;
	
	private $answer_elements;
	
	private $defaults;
	
	protected function build_creation_form()
	{
		// TODO: Some sensible defaults based on the type, especially for count fields
		// TODO: Extract methods
		parent::build_creation_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$step = 0;
		$this->type_element = $this->add_select(
			LearningStyleSurvey :: PROPERTY_SURVEY_TYPE,
			Translation :: get('SurveyType'),
			LearningStyleSurveyModel :: get_known_types()
		);
		// Entered default survey properties?
		if ($this->validate())
		{
			$step = 1;
			$this->type_element->freeze();
			$this->category_count_element = $this->add_textfield(self :: PARAM_CATEGORY_COUNT, Translation :: get('SurveyCategoryCount'));
			$this->section_count_element = $this->add_textfield(self :: PARAM_SECTION_COUNT, Translation :: get('SurveySectionCount'));
			// Entered category count and section count?
			if ($this->validate())
			{
				$step = 2;
				$this->category_count_element->freeze();
				$this->section_count_element->freeze();
				$category_count = intval($this->category_count_element->exportValue());
				$section_count = intval($this->section_count_element->exportValue());
				$this->category_elements = array();
				foreach (range(1, $category_count) as $i) {
					$name = self :: PARAM_CATEGORY_NAME . $i;
					$name_el = $this->add_textfield($name, Translation :: get('SurveyCategoryName') . ' ' . $i);
					$desc_el = $this->add_html_editor(self :: PARAM_CATEGORY_DESCRIPTION . $i, Translation :: get('SurveyCategoryDescription') . ' ' . $i);
					$this->category_elements[$i] = array(
						'name' => $name_el,
						'description' => $desc_el
					);
				}
				$this->section_elements = array();
				foreach (range(1, $section_count) as $i) {
					$name = self :: PARAM_SECTION_TITLE . $i;
					$title_el = $this->add_textfield($name, Translation :: get('SurveySectionTitle') . ' ' . $i);
					$intro_el = $this->add_html_editor(self :: PARAM_SECTION_INTRODUCTION . $i, Translation :: get('SurveySectionIntroduction') . ' ' . $i);
					$this->section_elements[$i] = array(
						'title' => $title_el,
						'introduction' => $intro_el
					);
				}
				// Entered categories and sections?
				if ($this->validate())
				{
					$step = 3;
					foreach ($this->category_elements as $els)
					{
						foreach ($els as $id => $el)
						{
							$el->freeze();
						}
					}
					$this->question_count_elements = array();
					foreach ($this->section_elements as $section => $els)
					{
						foreach ($els as $id => $el)
						{
							$el->freeze();
						}
						$name = self :: PARAM_QUESTION_COUNT . $section;
						$this->question_count_elements[$section] = $this->add_textfield($name, Translation :: get('SurveySectionQuestionCount') . ' ' . $section);
					}
					// Entered question counts?
					if ($this->validate())
					{
						$step = 4;
						$this->question_elements = array();
						$this->answer_elements = array();
						$survey_type = $this->get_survey_type();
						foreach ($this->question_count_elements as $elem)
						{
							$elem->freeze();
						}
						$categories = array();
						foreach ($this->category_elements as $category => $els)
						{
							$categories[$category] = $els['name']->exportValue();
						}
						foreach ($this->section_elements as $section => $els)
						{
							foreach (range(1, intval($this->question_count_elements[$section]->exportValue())) as $question)
							{
								$name = self :: PARAM_QUESTION . $section . '_' . $question;
								$this->question_elements[$section][$question]['text'] = $this->add_html_editor($name, Translation :: get('SurveySectionQuestion') . ' ' . $section . '.' . $question);
								// TODO: use model
								if ($survey_type == LearningStyleSurveyModel :: TYPE_PROPOSITION_AGREEMENT)
								{
									$name = self :: PARAM_QUESTION_CATEGORY . $section . '_' . $question;
									$this->question_elements[$section][$question]['categories'] = $this->add_select($name, Translation :: get('SurveySectionQuestionCategories') . ' ' . $section . '.' . $question, $categories, false, array('size' => count($categories), 'multiple' => 'multiple'));
								}
								elseif ($survey_type == LearningStyleSurveyModel :: TYPE_ANSWER_ORDERING)
								{
									$name = self :: PARAM_ANSWER_COUNT . $section . '_' . $question;
									$this->question_elements[$section][$question]['answers'] = $this->add_textfield($name, Translation :: get('SurveySectionQuestionAnswerCount') . ' ' . $section . '.' . $question);
								}
								else
								{
									// Won't happen, but hey.
									die('Invalid survey type');
								}
							}
						}
						// Entered answer counts? (if relevant)
						// TODO: use model
						if ($survey_type == LearningStyleSurveyModel :: TYPE_ANSWER_ORDERING && $this->validate())
						{
							$step = 5;
							$this->answer_elements = array();
							foreach ($this->section_elements as $section => $els)
							{
								foreach ($this->question_elements[$section] as $question => $data)
								{
									$data['text']->freeze();
									$answer_count = $data['answers'];
									$answer_count->freeze();
									foreach (range(1, intval($answer_count->exportValue())) as $answer)
									{
										$name = self :: PARAM_ANSWER . $section . '_' . $question . '_' . $answer;
										$this->answer_elements[$section][$question][$answer]['text'] = $this->add_html_editor($name, Translation :: get('SurveySectionQuestionAnswer') . ' ' . $section . '.' . $question . '.' . $answer);
										$name = self :: PARAM_ANSWER_CATEGORY . $section . '_' . $question . '_' . $answer;
										$this->answer_elements[$section][$question][$answer]['categories'] = $this->add_select($name, Translation :: get('SurveySectionAnswerCategories') . ' ' . $section . '.' . $question . '.' . $answer, $categories, false, array('size' => count($categories), 'multiple' => 'multiple'));
									}
								}
							}
						}
					}
				}
			}
		}
		$submitted_step = intval($_REQUEST[self :: PARAM_STEP]);
		if ($submitted_step != $step)
		{
			$this->set_error_reporting(false);
		}
		$this->addElement('html', '<input type="hidden" name="' . self :: PARAM_STEP . '" value="' . $step . '"/>');
		$this->addElement('category');
	}
	
	protected function build_editing_form()
	{
		// TODO: avoid code duplication
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->defaults = array();
		$survey = $this->get_learning_object();
		$survey_type = $survey->get_survey_type();
		$this->category_elements = array();
		$category_options = array();
		foreach ($survey->get_survey_categories() as $index => $category)
		{
			$i = $index + 1;
			$name = self :: PARAM_CATEGORY_NAME . $i;
			$name_el = $this->add_textfield($name, Translation :: get('SurveyCategoryName') . ' ' . $i);
			$desc_el = $this->add_html_editor(self :: PARAM_CATEGORY_DESCRIPTION . $i, Translation :: get('SurveyCategoryDescription') . ' ' . $i);
			$this->category_elements[$category->get_id()] = array(
				'name' => $name_el,
				'description' => $desc_el
			);
			$this->defaults[$name_el->getName()] = $category->get_title();
			$this->defaults[$desc_el->getName()] = $category->get_description();
			$category_options[$category->get_id()] = $category->get_title();
		}
		$this->section_elements = array();
		$this->question_elements = array();
		$this->answer_elements = array();
		foreach ($survey->get_survey_sections() as $index => $section)
		{
			$i = $index + 1;
			$name = self :: PARAM_SECTION_TITLE . $i;
			$title_el = $this->add_textfield($name, Translation :: get('SurveySectionTitle') . ' ' . $i);
			$intro_el = $this->add_html_editor(self :: PARAM_SECTION_INTRODUCTION . $i, Translation :: get('SurveySectionIntroduction') . ' ' . $i);
			$this->section_elements[$section->get_id()] = array(
				'title' => $title_el,
				'introduction' => $intro_el
			);
			$this->defaults[$title_el->getName()] = $section->get_title();
			$this->defaults[$intro_el->getName()] = $section->get_description();
			foreach ($section->get_section_questions() as $qindex => $question)
			{
				$j = $qindex + 1;
				$name = self :: PARAM_QUESTION . $i . '_' . $j;
				$el = $this->add_html_editor($name, Translation :: get('SurveySectionQuestion') . ' ' . $i . '.' . $j);
				$this->question_elements[$question->get_id()]['text'] = $el;
				$this->defaults[$el->getName()] = $question->get_description();
				// TODO: use model
				if ($survey_type == LearningStyleSurveyModel :: TYPE_PROPOSITION_AGREEMENT)
				{
					$name = self :: PARAM_QUESTION_CATEGORY . $i . '_' . $j;
					$el = $this->add_select($name, Translation :: get('SurveySectionQuestionCategories') . ' ' . $i . '.' . $j, $category_options, false, array('size' => count($category_options), 'multiple' => 'multiple'));
					$this->question_elements[$question->get_id()]['categories'] = $el;
					$this->defaults[$el->getName()] = $question->get_question_category_ids();
				}
				elseif ($survey_type == LearningStyleSurveyModel :: TYPE_ANSWER_ORDERING)
				{
					foreach ($question->get_question_answers() as $aindex => $answer)
					{
						$k = $aindex + 1;
						$name = self :: PARAM_ANSWER . $i . '_' . $j . '_' . $k;
						$text_el = $this->add_html_editor($name, Translation :: get('SurveySectionQuestionAnswer') . ' ' . $i . '.' . $j . '.' . $k);
						$name = self :: PARAM_ANSWER_CATEGORY . $i . '_' . $j . '_' . $k;
						$cat_el = $this->add_select($name, Translation :: get('SurveySectionAnswerCategories') . ' ' . $i . '.' . $j . '.' . $k, $category_options, false, array('size' => count($category_options), 'multiple' => 'multiple'));
						$this->answer_elements[$answer->get_id()]['text'] = $text_el;
						$this->answer_elements[$answer->get_id()]['categories'] = $cat_el;
						$this->defaults[$text_el->getName()] = $answer->get_description();
						$this->defaults[$cat_el->getName()] = $answer->get_answer_category_ids();
					}
				}
			}
		}
		$this->addElement('category');
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
		foreach ($this->category_elements as $cid => $els)
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
		foreach ($this->section_elements as $sid => $els)
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
			foreach ($this->question_elements[$sid] as $qid => $data)
			{
				$question_text = $data['text']->exportValue();
				$question = new LearningStyleSurveyQuestion();
				$question->set_owner_id($this->get_owner_id());
				$question->set_title($survey->get_title() . ' Q#' . $sid . '.' . $qid);
				$question->set_description($question_text);
				$question->set_parent_id($section->get_id());
				if ($survey_type == LearningStyleSurveyModel :: TYPE_PROPOSITION_AGREEMENT)
				{
					$cat = array();
					foreach ($data['categories']->getValue() as $index)
					{
						$cat[] = $categories[$index];
					}
					$question->set_question_category_ids($cat);
				}
				$question->create();
				if ($survey_type == LearningStyleSurveyModel :: TYPE_ANSWER_ORDERING)
				{
					// Answers to question
					foreach ($this->answer_elements[$sid][$qid] as $aid => $adata) {
						$answer_text = $adata['text']->exportValue();
						$answer = new LearningStyleSurveyAnswer();
						$answer->set_owner_id($this->get_owner_id());
						$answer->set_title($survey->get_title() . ' A#' . $sid . '.' . $qid . '.' . $aid);
						$answer->set_description($answer_text);
						$answer->set_parent_id($question->get_id());
						$cat = array();
						foreach ($adata['categories']->getValue() as $index)
						{
							$cat[] = $categories[$index];
						}
						$answer->set_answer_category_ids($cat);
						$answer->create();
					}
				}
			}
		}
		return $return_value;
	}
	
	function update_learning_object()
	{
		$survey = $this->get_learning_object();
		$survey_type = $survey->get_survey_type();
		foreach ($survey->get_survey_categories() as $category)
		{
			$el = $this->category_elements[$category->get_id()];
			$category->set_title($el['name']->exportValue());
			$category->set_description($el['description']->exportValue());
			$category->update();
		}
		foreach ($survey->get_survey_sections() as $section)
		{
			$el = $this->section_elements[$section->get_id()];
			$section->set_title($el['title']->exportValue());
			$section->set_description($el['introduction']->exportValue());
			$section->update();
			foreach ($section->get_section_questions() as $question)
			{
				$el = $this->question_elements[$question->get_id()];
				$question->set_description($el['text']->exportValue());
				if ($survey_type == LearningStyleSurveyModel :: TYPE_PROPOSITION_AGREEMENT)
				{
					$question->set_question_category_ids($el['categories']->getValue());
				}
				elseif ($survey_type == LearningStyleSurveyModel :: TYPE_ANSWER_ORDERING)
				{
					foreach ($question->get_question_answers() as $answer)
					{
						$el = $this->answer_elements[$answer->get_id()];
						$answer->set_description($el['text']->exportValue());
						$answer->set_answer_category_ids($el['categories']->getValue());
						$answer->update();
					}
				}
				$question->update();
			}
		}
		return parent :: update_learning_object();
	}
	
	function setDefaults ($defaults = array())
	{
		if (count($this->defaults))
		{
			$defaults = array_merge($defaults, $this->defaults);
		}
		parent :: setDefaults($defaults);
	}
	
	private function get_survey_type() {
		return $this->type_element->exportValue();
	}
}

?>