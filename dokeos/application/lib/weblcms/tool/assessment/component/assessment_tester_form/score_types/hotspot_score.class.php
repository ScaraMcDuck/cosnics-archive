<?php
require_once dirname(__FILE__).'/../score.class.php';

class HotspotScore extends Score
{
	function get_score()
	{
		$answers = $this->get_question()->get_answers();
		$num = $this->get_answer_num();
		$answer = $this->get_answer();
		
		$parts = split('-', $answer);
		$correct = $parts[1];
		if ($correct == 1)
		{
			return $answers[$num]->get_weight();
		}
		else
		{
			return 0;
		}
	}
}
?>