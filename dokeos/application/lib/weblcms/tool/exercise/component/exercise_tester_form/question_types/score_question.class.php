<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class ScoreQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		parent :: add_to($formvalidator);
		$answers = $this->get_answers();
		$minscore = $answers[0];
		$maxscore = $answers[1];
		
		$min = $minscore['score'];
		$max = $maxscore['score'];
	
		for ($i = $min; $i <= $max; $i++)
		{
			$scores[$i] = $i;
		}
		$formvalidator->addElement('select',$this->get_clo_question()->get_ref(), 'Score:',$scores);
		$formvalidator->addElement('html', '<br />');
	}
}
?>