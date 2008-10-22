<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class ScoreQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref(), 'question');
		
		$answers = $this->get_answers($question->get_id());
		$formvalidator->addElement('html','Point rating'.$question->get_description().'<br/>');
		$minscore = $answers[0];
		$maxscore = $answers[1];
		
		$min = $minscore['score'];
		$max = $maxscore['score'];
	
		for ($i = $min; $i <= $max; $i++)
		{
			$scores[$i] = $i;
		}
		$formvalidator->addElement('select',$question->get_id(), 'Score:',$scores);
	}
}
?>