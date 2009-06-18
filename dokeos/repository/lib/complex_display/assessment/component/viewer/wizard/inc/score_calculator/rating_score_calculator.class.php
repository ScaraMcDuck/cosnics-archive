<?php
require_once dirname(__FILE__).'/../score_calculator.class.php';

class ScoreCalculatorScoreCalculator extends ScoreCalculator
{
	
	function get_score()
	{
		$question = parent :: get_question();
		if ($question->get_correct() == null)
			return parent :: get_answer();
		else
		{
			if (parent :: get_answer() == $question->get_correct())
				return $question->get_high();
			else
				return 0;
		}
	}
}
?>