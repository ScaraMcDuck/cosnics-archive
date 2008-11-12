<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class PercentageQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$html[] = $this->display_question();
		
		$user_answers = parent :: get_user_answers();
		
		$html[] = '<div class="learning_object" style="">';
		$html[] = '<div class="title">';
		$html[] = 'Your answer: '.$user_answers[0]->get_extra().'/100';
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