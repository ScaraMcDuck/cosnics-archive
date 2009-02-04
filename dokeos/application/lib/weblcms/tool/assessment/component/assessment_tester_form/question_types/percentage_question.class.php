<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class PercentageQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		$formvalidator->addElement('html', parent :: display_header());
		for ($i = 0; $i <= 100; $i++)
		{
			$scores[$i] = $i;
		}
		$formvalidator->addElement('select',$this->get_clo_question()->get_ref().'_0',$scores);
		//$formvalidator->addElement('html', '<br />');
		$formvalidator->addElement('html', $this->display_footer());
	}
}
?>