<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class PercentageQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref(), 'question');
		
		$formvalidator->addElement('html','Percentage rating'.$question->get_description().'<br/>');
		for ($i = 0; $i <= 100; $i++)
		{
			$scores[$i] = $i;
		}
		$formvalidator->addElement('select',$question->get_id(), 'Score:',$scores);
	}
}
?>