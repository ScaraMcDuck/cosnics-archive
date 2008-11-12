<?php

require_once dirname(__FILE__).'/question_result_types/document_question.class.php';
require_once dirname(__FILE__).'/question_result_types/fill_in_blanks_question.class.php';
require_once dirname(__FILE__).'/question_result_types/matching_question.class.php';
require_once dirname(__FILE__).'/question_result_types/multiple_answer_question.class.php';
require_once dirname(__FILE__).'/question_result_types/multiple_choice_question.class.php';
require_once dirname(__FILE__).'/question_result_types/open_question_with_document.class.php';
require_once dirname(__FILE__).'/question_result_types/open_question.class.php';
require_once dirname(__FILE__).'/question_result_types/percentage_question.class.php';
require_once dirname(__FILE__).'/question_result_types/score_question.class.php';
require_once dirname(__FILE__).'/question_result_types/yes_no_question.class.php';

abstract class QuestionResult
{
	private $user_question;
	private $question;
	
	private $clo_question;
	private $user_answers;
	private $clo_answers;
	
	function QuestionResult($user_question, $question) 
	{
		$this->question = $question;
		$this->user_question = $user_question;
		$this->init();
	}
	
	function init()
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $this->user_question->get_question_id());
		$this->clo_question = $dm->retrieve_complex_learning_object_items($condition)->next_result();
		$condition = new EqualityCondition(UserAnswer :: PROPERTY_USER_QUESTION_ID, $this->user_question->get_id());
		$answers = WeblcmsDataManager :: get_instance()->retrieve_user_answers($condition);
		
		while ($user_answer = $answers->next_result())
		{
			$this->user_answers[] = $user_answer;
		}
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->question->get_id());
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		while ($clo_answer = $clo_answers->next_result())
		{
			$this->clo_answers[] = $clo_answer;
		}
	}
	
	function get_user_question()
	{
		return $this->user_question;
	}
	
	function get_question()
	{
		return question;
	}
	
	function get_clo_question()
	{
		return $this->clo_question;
	}
	
	function get_user_answers()
	{
		return $this->user_answers;
	}
	
	function get_clo_answers()
	{
		return $this->clo_answers;
	}
	
	function display_question()
	{
		$learning_object = $this->question;
		$html[] = '<div class="learning_object" style="background-image: url('. Theme :: get_common_img_path(). 'learning_object/' .$learning_object->get_icon_name().'.png);">';
		$html[] = '<div class="title">';
		$html[] = 'Question: '.$learning_object->get_title();
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = $learning_object->get_description();
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	static function create_question_result($user_question)
	{
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_question->get_question_id());
		switch ($question->get_question_type())
		{
			case Question :: TYPE_DOCUMENT:
				return new DocumentQuestionResult($user_question, $question);
			case Question :: TYPE_FILL_IN_BLANKS:
				return new FillInBlanksQuestionResult($user_question, $question);
			case Question :: TYPE_MATCHING:
				return new MatchingQuestionResult($user_question, $question);
			case Question :: TYPE_MULTIPLE_ANSWER:
				return new MultipleAnswerQuestionResult($user_question, $question);
			case Question :: TYPE_MULTIPLE_CHOICE:
				return new MultipleChoiceQuestionResult($user_question, $question);
			case Question :: TYPE_OPEN:
				return new OpenQuestionResult($user_question, $question);
			case Question :: TYPE_OPEN_WITH_DOCUMENT:
				return new OpenQuestionWithDocumentResult($user_question, $question);
			case Question :: TYPE_PERCENTAGE:
				return new PercentageQuestionResult($user_question, $question);
			case Question :: TYPE_SCORE:
				return new ScoreQuestionResult($user_question, $question);
			case Question :: TYPE_YES_NO:
				return new YesNoQuestionResult($user_question, $question);
			default:
				return null;
		}
	}
	
	abstract function display_exercise();
	
	abstract function display_survey();
	
	abstract function display_assignment();
}
?>