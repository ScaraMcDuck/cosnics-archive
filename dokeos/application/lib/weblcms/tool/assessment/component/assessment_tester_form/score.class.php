<?php
//require_once dirname(__FILE__).'/score_types/document_score.class.php';
require_once dirname(__FILE__).'/score_types/fill_in_blanks_score.class.php';
//require_once dirname(__FILE__).'/score_types/multiple_answer_score.class.php';
require_once dirname(__FILE__).'/score_types/multiple_choice_score.class.php';
require_once dirname(__FILE__).'/score_types/open_question_score.class.php';
//require_once dirname(__FILE__).'/score_types/open_question_with_document_score.class.php';
//require_once dirname(__FILE__).'/score_types/percentage_score.class.php';
require_once dirname(__FILE__).'/score_types/score_score.class.php';
//require_once dirname(__FILE__).'/score_types/yes_no_score.class.php';
require_once dirname(__FILE__).'/score_types/matching_score.class.php';
//require_once dirname(__FILE__).'/score_types/document_score.class.php';

abstract class Score
{
	private $answer;
	private $question;
	private $answer_num;
	
	function Score($answer, $question, $answer_num)
	{
		$this->answer = $answer;
		$this->question = $question;
		$this->answer_num = $answer_num;
	}
	
	abstract function get_score();
	
	function get_answer()
	{
		return $this->answer;
	}
	
	function get_answer_num()
	{
		return $this->answer_num;
	}
	
	function get_question()
	{
		return $this->question;
	}
	
	static function factory($answer, $question, $answer_num)
	{
		switch($question->get_type())
		{
			case 'open_question':
				$score_type = new OpenQuestionScore($answer, $question, $answer_num);
				break;
			case 'fill_in_blanks_question':
				$score_type = new FillInBlanksScore($answer, $question, $answer_num);
				break;
			case 'matching_question':
				$score_type = new MatchingScore($answer, $question, $answer_num);
				break;
			case 'multiple_choice_question':
				$score_type = new MultipleChoiceScore($answer, $question, $answer_num);
				break;
			case 'rating_question':
				$score_type = new ScoreScore($answer, $question, $answer_num);
				break;
			case 'hotspot_question':
				$score_type = new HotspotScore($answer, $question, $answer_num);
				break;
			default:
				$score_type = null;
		}
		return $score_type;
	}
}
?>