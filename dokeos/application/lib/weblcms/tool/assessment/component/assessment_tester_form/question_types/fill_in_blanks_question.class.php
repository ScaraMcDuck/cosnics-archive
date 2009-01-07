<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class FillInBlanksQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		$formvalidator->addElement('html', parent :: display_header());
		$answers = $this->get_answers();
		
		$i = 1;
		foreach($answers as $answer)
		{
			$name = $this->get_clo_question()->get_ref().'_'.$answer['answer']->get_id();
			$formvalidator->addElement('text', $name, '('.$i.')');
			//$formvalidator->addRule($name, Translation :: get('ThisFieldIsRequired'), 'required');
			$i++;
		}
		$formvalidator->addElement('html', '<br />');
		$formvalidator->addElement('html', $this->display_footer());
	}
}
?>