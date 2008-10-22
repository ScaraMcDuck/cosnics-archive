<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class FillInBlanksQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		$clo_question = $this->get_clo_question();
		$question = $clo_question->get_learning_object();
		$question_weight = $clo_question->get_weight();
		$question_id = $question->get_id();
		$question_type = $question->get_question_type();
		$descr = $question['question']->get_description();
		
		$answers = $this->get_answers($question_id);
		
		$this->add_question($question, $answers, $type);
	}
	
}
?>