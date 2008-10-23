<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class OpenQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		parent :: add_to($formvalidator);
		$answers = $this->get_answers();
		
		//$formvalidator->addElement('html','<div class="learning_object">');
		//foreach($answers as $answer)
		//{
			$formvalidator->addElement('html_editor', $this->get_clo_question()->get_ref(), '');
		//}
		//$formvalidator->addElement('html','</div>');
		$formvalidator->addElement('html', '<br/>');
	}
}
?>