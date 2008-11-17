<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class OpenQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		parent :: add_to($formvalidator);
		$answers = $this->get_answers();
		$name = $this->get_clo_question()->get_ref().'_0';
		$formvalidator->addElement('html_editor', $name, '');
		$formvalidator->addRule($name, Translation :: get('ThisFieldIsRequired'), 'required');
		$formvalidator->addElement('html', '<br/>');
	}
}
?>