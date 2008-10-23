<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class FillInBlanksQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		parent :: add_to($formvalidator);
		$answers = $this->get_answers();
		
		$i = 1;
		foreach($answers as $answer)
		{
			$formvalidator->addElement('text', $this->get_clo_question()->get_ref(), '('.$i.')');
			$i++;
		}
		$formvalidator->addElement('html', '<br />');
	}
}
?>