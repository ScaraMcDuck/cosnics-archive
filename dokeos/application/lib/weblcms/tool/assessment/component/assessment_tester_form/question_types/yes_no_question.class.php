<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class YesNoQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		$formvalidator->addElement('html', parent :: display_header());
		$answers = $this->get_answers();
		
		$formvalidator->addElement('html','Yes/no question'.$question->get_description().' Points:'.$clo_question->get_weight().'<br/>');
		foreach($answers as $answer)
		{
			$formvalidator->addElement('radio', $this->get_clo_question()->get_ref().'_'.$answer['answer']->get_id(), $answer['answer']->get_title());
		}
		$formvalidator->addElement('html', '<br />');
		$formvalidator->addElement('html', $this->display_footer());
	}
}
?>