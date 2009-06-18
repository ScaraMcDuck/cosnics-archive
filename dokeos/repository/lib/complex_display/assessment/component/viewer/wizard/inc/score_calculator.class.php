<?php

/**
 * Abstract class so each question type can determine the correct score with the given answers
 *
 */
abstract class ScoreCalculator
{
	private $answer;
	private $question;
	
	function ScoreCalculator($question, $answer)
	{
		$this->answer = $answer;
		$this->question = $question;
	}
	
	abstract function get_score();
	
	function get_answer()
	{
		return $this->answer;
	}

	function get_question()
	{
		return $this->question;
	}
	
	static function factory($question, $answer)
	{
		$type = $question->get_type();

		$file = dirname(__FILE__) . '/score_calculator/' . $type . '_score_calculator.class.php';

		if(!file_exists($file))
		{
			die('file does not exist: ' . $file);
		}

		require_once $file;

		$class = DokeosUtilities :: underscores_to_camelcase($type) . 'ScoreCalculator';
		$score_calculator = new $class($question, $answer);
		return $score_calculator;
	}
}
?>