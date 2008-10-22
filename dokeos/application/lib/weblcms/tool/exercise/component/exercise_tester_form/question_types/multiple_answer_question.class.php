<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class MultipleAnswerQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref(), 'question');
		
		$answers = $this->get_answers($question->get_id());
		
		$formvalidator->addElement('html','Multiple answer question'.$question->get_description().' Points:'.$clo_question->get_weight().'<br/>');
		foreach($answers as $answer)
		{
			$formvalidator->addElement('checkbox', $question->get_id(), $answer['answer']->get_description());
		}
	}
}
?>