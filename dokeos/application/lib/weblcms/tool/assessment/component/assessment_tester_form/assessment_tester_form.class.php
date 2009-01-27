<?php

require_once Path::get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/question_types/open_question.class.php';
require_once dirname(__FILE__).'/question_types/open_question_with_document.class.php';
require_once dirname(__FILE__).'/question_types/multiple_answer_question.class.php';
require_once dirname(__FILE__).'/question_types/multiple_choice_question.class.php';
require_once dirname(__FILE__).'/question_types/fill_in_blanks_question.class.php';
require_once dirname(__FILE__).'/question_types/matching_question.class.php';
require_once dirname(__FILE__).'/question_types/percentage_question.class.php';
require_once dirname(__FILE__).'/question_types/score_question.class.php';
require_once dirname(__FILE__).'/question_types/yes_no_question.class.php';
require_once dirname(__FILE__).'/question_types/document_question.class.php';

class AssessmentTesterForm extends FormValidator
{
	
	function AssessmentTesterForm($assessment, $url, $page)
	{
		parent :: __construct('assessment', 'post', $url);
		$this->initialize($assessment, $page);
	}
	
	function initialize($assessment, $page) 
	{
		$assessment_id = $assessment->get_id();
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $assessment_id);
		$clo_questions = $dm->retrieve_complex_learning_object_items($condition);
		
		$this->addElement('html', '<br/><div class="learning_object" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/' .$assessment->get_icon_name().'.png);">');
		$this->addElement('html', '<div class="title" style="font-size: 14px">');
		$this->addElement('html', Translation :: get('TakeAssessment').': '.$assessment->get_title());
		$this->addElement('html', '</div>');
		$this->addElement('html', '<div class="description">');
		$this->addElement('html', $assessment->get_description());
		$this->addElement('html', '</div>');
		$this->addElement('html', '</div>');
		
		$start_question = ($page - 1) * $assessment->get_questions_per_page() + 1;
		$stop_question = $start_question + $assessment->get_questions_per_page();
		$count = 1;
		while($clo_question = $clo_questions->next_result())
		{
			if ($start_question != $stop_question)
			{
				if ($count >= $start_question && $count < $stop_question)
				{
					$question_display = QuestionDisplay :: factory($clo_question);
					if (isset($question_display))
						$question_display->add_to($this);
						
					$this->addElement('html', '<br />');
				}
				$count++;
			}
			else
			{
				$question_display = QuestionDisplay :: factory($clo_question);
				if (isset($question_display))
					$question_display->add_to($this);
					
				$this->addElement('html', '<br />');
			}
		}
		//$this->addElement('submit', 'submit', Translation :: get('Submit'));
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}
}
?>