<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class ScoreQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$html[] = $this->display_question();
		
		$user_answers = parent :: get_user_answers();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $user_answers[0]->get_answer_id());
		$clo_answers = parent :: get_clo_answers();
		$low = $clo_answers[0];
		$high = $clo_answers[1];
		
		$html[] = '<div class="learning_object" style="">';
		$html[] = '<div class="title">';
		$html[] = 'Your answer: '.$user_answers[0]->get_extra().' (from '.$low->get_score().' to '.$high->get_score().')';
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