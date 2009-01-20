<?php
require_once dirname(__FILE__).'/score_types/document_score.class.php';
require_once dirname(__FILE__).'/score_types/fill_in_blanks_score.class.php';
require_once dirname(__FILE__).'/score_types/multiple_answer_score.class.php';
require_once dirname(__FILE__).'/score_types/multiple_choice_score.class.php';
require_once dirname(__FILE__).'/score_types/open_question_score.class.php';
require_once dirname(__FILE__).'/score_types/open_question_with_document_score.class.php';
require_once dirname(__FILE__).'/score_types/percentage_score.class.php';
require_once dirname(__FILE__).'/score_types/score_score.class.php';
require_once dirname(__FILE__).'/score_types/yes_no_score.class.php';
require_once dirname(__FILE__).'/score_types/matching_score.class.php';
require_once dirname(__FILE__).'/score_types/document_score.class.php';

abstract class Score
{
	private $answer;
	private $user_answer;
	private $question;
	
	function Score($answer, $user_answer, $question)
	{
		$this->answer = $answer;
		$this->user_answer = $user_answer;
		$this->question = $question;
	}
	
	abstract function get_score();
	
	function get_answer()
	{
		return $this->answer;
	}
	
	function get_user_answer()
	{
		return $this->user_answer;
	}
	
	function get_question()
	{
		return $this->question;
	}
	
	static function factory($answer, $user_answer, $user_question)
	{
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_question->get_question_id());
	
		switch($question->get_question_type())
		{
			case Question :: TYPE_OPEN:
				$score_type = new OpenQuestionScore($answer, $user_answer, $question);
				break;
			case Question :: TYPE_OPEN_WITH_DOCUMENT:
				$score_type = new OpenQuestionWithDocumentScore($answer, $user_answer, $question);
				break;
			case Question :: TYPE_DOCUMENT:
				$score_type = new DocumentScore($answer, $user_answer, $question);
				break;
			case Question :: TYPE_FILL_IN_BLANKS:
				$score_type = new FillInBlanksScore($answer, $user_answer, $question);
				break;
			case Question :: TYPE_MATCHING:
				$score_type = new MatchingScore($answer, $user_answer, $question);
				break;
			case Question :: TYPE_MULTIPLE_ANSWER:
				$score_type = new MultipleAnswerScore($answer, $user_answer, $question);
				break;
			case Question :: TYPE_MULTIPLE_CHOICE:
				$score_type = new MultipleChoiceScore($answer, $user_answer, $question);
				break;
			case Question :: TYPE_PERCENTAGE:
				$score_type = new PercentageScore($answer, $user_answer, $question);
				break;
			case Question :: TYPE_SCORE:
				$score_type = new ScoreScore($answer, $user_answer, $question);
				break;
			case Question :: TYPE_YES_NO:
				$score_type = new YesNoScore($answer, $user_answer, $question);
				break;
			default:
				$score_type = null;
		}
		return $score_type;
	}
}
?>