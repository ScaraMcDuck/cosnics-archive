<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class OpenQuestionWithDocumentResult extends QuestionResult
{
	function display_exercise()
	{
		$html[] = $this->display_question();
		
		$user_question = $this->get_user_question();
		$user_answers = $this->get_user_answers();
		$user_answer = $user_answers[0];
		$user_score = $user_answer->get_score();
		
		$html[] = '<div class="learning_object" style="">';
		$html[] = '<div class="title">';
		$html[] = 'Your answer: (Score: '.$user_score.'/'.$user_question->get_weight().')';
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = $user_answer->get_extra();
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode('<br/>', $html);
	}
	
	function display_survey()
	{
		
	}
	
	function display_assignment()
	{
		$html[] = $this->display_question();
		return implode('<br/>', $html);
	}
}
?>