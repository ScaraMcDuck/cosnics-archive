<?php
require_once dirname(__FILE__).'/../score_calculator.class.php';

class FillInBlanksScoreCalculator extends ScoreCalculator
{
	
	function calculate_score()
	{
		$blanks = $this->get_question()->get_answers();
		$question = $this->get_question();
		$answers = $this->get_answer();
		
	}
}
?>