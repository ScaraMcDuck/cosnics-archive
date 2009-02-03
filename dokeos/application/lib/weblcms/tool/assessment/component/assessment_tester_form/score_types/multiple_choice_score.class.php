<?php
require_once dirname(__FILE__).'/../score.class.php';

class MultipleChoiceScore extends Score
{
	
	function get_score()
	{
		$question = $this->get_question();
		if ($question->get_answer_type() == 'radio')
		{
			$answers = $question->get_options();
			$selected = $answers[$this->get_answer()];
			if ($selected->is_correct())
				return $selected->get_weight();
			else
				return 0;
		}
		else
		{

			$answers = $question->get_options();
			$answer = $answers[$this->get_answer_num()-1];
			return $answer->get_weight();
		}
	}
}
?>