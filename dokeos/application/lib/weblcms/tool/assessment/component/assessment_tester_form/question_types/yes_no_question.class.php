<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class YesNoQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		$formvalidator->addElement('html', parent :: display_header());
		$answers = $this->get_answers();
		$i = 0;
		foreach($answers as $answer)
		{
			$elements[] = $formvalidator->createElement('radio', null, null, $answer['answer']->get_title().'<br/>', $i);
			$i++;
		}
		$name = $this->get_clo_question()->get_ref().'_0';
		$formvalidator->addGroup($elements, $name, '<br/>');
		$formvalidator->addRule($name, Translation :: get('ThisFieldIsRequired'), 'required');
		$formvalidator->addElement('html', '<br />');
		$formvalidator->addElement('html', $this->display_footer());
	}
}
?>