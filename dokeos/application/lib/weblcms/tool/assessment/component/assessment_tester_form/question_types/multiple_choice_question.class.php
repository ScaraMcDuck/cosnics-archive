<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class MultipleChoiceQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		$formvalidator->addElement('html', parent :: display_header());
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$answers = $question->get_options();
		$type = $question->get_answer_type();
		
		if ($type == 'radio')
		{
			$i = 0;
			foreach($answers as $answer)
			{
				$elements[] = $formvalidator->createElement('radio', null, null, $answer->get_value().'<br/>', $i);
				$i++;
			}
			$name = $this->get_clo_question()->get_ref().'_0';
			$formvalidator->addGroup($elements, $name, '<br/>');
		}
		else if ($type == 'checkbox')
		{
			foreach($answers as $i => $answer)
			{
				$name = $this->get_clo_question()->get_ref().'_'.($i + 1);
				$formvalidator->addElement('checkbox', $name, '', $answer->get_value());
			}
		}
		$formvalidator->addElement('html', '<br />');
		$formvalidator->addElement('html', $this->display_footer());
	}
}
?>