<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class RatingQuestionDisplay extends QuestionDisplay
{
	function add_question_form($formvalidator)
	{
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$min = $question->get_low();
		$max = $question->get_high();

		for ($i = $min; $i <= $max; $i++)
		{
			$scores[$i] = $i;
		}
		$formvalidator->addElement('select',$this->get_clo_question()->get_id().'_0', Translation :: get('Rating') . ': ',$scores);
	}

	function add_borders()
	{
		return true;
	}
}
?>