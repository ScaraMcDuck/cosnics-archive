<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class FillInBlanksQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$html[] = $this->display_question();

		$user_answers = parent :: get_user_answers();
		$rdm = RepositoryDataManager :: get_instance();

		$clo_answers = parent :: get_clo_answers();
		foreach ($clo_answers as $clo_answer)
		{
			$total_div += $clo_answer->get_score();
		}
		
		foreach ($user_answers as $user_answer) 
		{
			$total_score += $user_answer->get_score();
		}
		
		$html[] = '<div class="learning_object" style="">';
		$html[] = '<div class="title">';
		$html[] = 'Your answer(s): (Score: '.$total_score.'/'.$total_div.')';
		$html[] = '</div>';
		$html[] = '<div class="description">';
		foreach ($user_answers as $user_answer)
		{
			$html[] = $user_answer->get_extra().' Score: '.$user_answer->get_score();
		}
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