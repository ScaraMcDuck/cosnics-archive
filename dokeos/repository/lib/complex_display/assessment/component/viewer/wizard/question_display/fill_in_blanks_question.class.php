<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class FillInBlanksQuestionDisplay extends QuestionDisplay
{
	function add_question_form($formvalidator)
	{
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$answers = $question->get_answers();
		
		foreach($answers as $i => $answer)
		{
			$name = $clo_question->get_id().'_'.$i;
			$formvalidator->addElement('text', $name, '('.$i.')');
		}
	}
}
?>