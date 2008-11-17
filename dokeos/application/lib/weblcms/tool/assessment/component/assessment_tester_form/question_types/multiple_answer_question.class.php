<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class MultipleAnswerQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		parent :: add_to($formvalidator);
		$answers = $this->get_answers();
		foreach($answers as $answer)
		{
			$name = $this->get_clo_question()->get_ref().'_'.$answer['answer']->get_id();
			$formvalidator->addElement('checkbox', $name, '', $answer['answer']->get_title());
			//$formvalidator->addRule($name, Translation :: get('ThisFieldIsRequired'), 'required');
		}
		$formvalidator->addElement('html', '<br />');
	}
}
?>