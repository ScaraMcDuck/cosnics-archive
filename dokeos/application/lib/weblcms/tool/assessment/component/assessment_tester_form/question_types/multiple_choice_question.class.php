<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class MultipleChoiceQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		parent :: add_to($formvalidator);
		$answers = $this->get_answers();
		foreach($answers as $answer)
		{
			$formvalidator->addElement('radio', $this->get_clo_question()->get_ref(), '', $answer['answer']->get_title());
		}
		$formvalidator->addElement('html', '<br />');
	}
}
?>