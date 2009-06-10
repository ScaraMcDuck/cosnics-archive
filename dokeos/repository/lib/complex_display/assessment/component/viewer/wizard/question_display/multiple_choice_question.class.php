<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class MultipleChoiceQuestionDisplay extends QuestionDisplay
{
	function add_question_form($formvalidator)
	{
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$answers = $question->get_options();
		$type = $question->get_answer_type();
		
		if ($type == 'radio')
		{
			$i = 0;
			foreach($answers as $answer)
			{
				$answer_text = substr($answer->get_value(), 3, strlen($answer->get_value()) - 7);
				$elements[] = $formvalidator->createElement('radio', null, null, $answer_text, $i);
				$i++;
			}
			$name = $this->get_clo_question()->get_id().'_0';
			$formvalidator->addGroup($elements, $name, null, '<br/>');
		}
		else if ($type == 'checkbox')
		{
			foreach($answers as $i => $answer)
			{
				$answer_text = substr($answer->get_value(), 3, strlen($answer->get_value()) - 7);
				$name = $this->get_clo_question()->get_id().'_'.($i + 1);
				$formvalidator->addElement('checkbox', $name, '', $answer_text);
			}
		}
	}
}
?>