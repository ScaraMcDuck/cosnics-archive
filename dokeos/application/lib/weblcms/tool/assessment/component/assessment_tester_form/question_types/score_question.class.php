<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class ScoreQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		$formvalidator->addElement('html', parent :: display_header());
		$answers = $this->get_answers();
		$minscore = $answers[0];
		$maxscore = $answers[1];
		
		$min = $minscore['score'];
		$max = $maxscore['score'];
	
		for ($i = $min; $i <= $max; $i++)
		{
			$scores[$i] = $i;
		}
		$formvalidator->addElement('select',$this->get_clo_question()->get_ref().'_0', 'Score:',$scores);
		$formvalidator->addElement('html', '<br />');
		$formvalidator->addElement('html', $this->display_footer());
	}
}
?>