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

class AssessmentTesterForm extends FormValidator
{
	
	function AssessmentTesterForm($assessment)
	{
		parent :: __construct('assessment', 'post');
		$this->initialize($assessment);
	}
	
	function initialize($assessment) 
	{
		$assessment_id = $assessment->get_id();
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $assessment_id);
		$clo_questions = $dm->retrieve_complex_learning_object_items($condition);
		
		while($clo_question = $clo_questions->next_result())
		{
			$question = $dm->retrieve_learning_object($clo_question->get_ref(), 'question');
			$type = $question->get_question_type();
			
			switch($type)
			{
			case Question :: TYPE_OPEN:
				$question_display = new OpenQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_OPEN_WITH_DOCUMENT:
				$question_display = new OpenQuestionWithDocumentDisplay($clo_question);
				break;
			case Question :: TYPE_FILL_IN_BLANKS:
				$question_display = new FillInBlanksQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_MATCHING:
				$question_display = new MatchingQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_MULTIPLE_ANSWER:
				$question_display = new MultipleAnswerQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_MULTIPLE_CHOICE:
				$question_display = new MultipleChoiceQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_PERCENTAGE:
				$question_display = new PercentageQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_SCORE:
				$question_display = new ScoreQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_YES_NO:
				$question_display = new YesNoQuestionDisplay($clo_question);
				break;
			default:
				$question_display = null;
			}
			if (isset($question_display))
				$question_display->add_to($this);
				
			$this->addElement('html', '<br />');
		}
		$this->addElement('submit', 'submit', Translation :: get('Submit'));
	}
}
?>