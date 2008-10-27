<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class MultipleChoiceQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		parent :: add_to($formvalidator);
		$answers = $this->get_answers();
		$i = 0;
		foreach($answers as $answer)
		{
			$elements[] = $formvalidator->createElement('radio', null, null, $answer['answer']->get_title().'<br/>', $i);
			$i++;
			//$formvalidator->addElement();
		}
		$formvalidator->addGroup($elements, $this->get_clo_question()->get_ref().'_0', '<br/>');
		$formvalidator->addElement('html', '<br />');
	}
}
?>