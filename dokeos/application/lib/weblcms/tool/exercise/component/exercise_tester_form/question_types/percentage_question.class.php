<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class PercentageQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		parent :: add_to($formvalidator);
		for ($i = 0; $i <= 100; $i++)
		{
			$scores[$i] = $i;
		}
		$formvalidator->addElement('select',$this->get_clo_question()->get_ref(), 'Score:',$scores);
		$formvalidator->addElement('html', '<br />');
	}
}
?>