<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class MultipleAnswerQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$html[] = $this->display_question_header();
		
		$user_question = $this->get_user_question();
		$user_answers = $this->get_user_answers();
		$clo_answers = $this->get_clo_answers();
		
		foreach ($user_answers as $user_answer)
		{
			$user_score += $user_answer->get_score();
		}
		foreach ($clo_answers as $clo_answer)
		{
			$user_score_div += $clo_answer->get_score();
		}
		
		$user_question_score = $user_score / $user_score_div * $user_question->get_weight();
		
		$score_line = Translation :: get('Score').': '.$user_question_score.'/'.$user_question->get_weight();
		$html[] = $this->display_score($score_line);
		
		foreach ($user_answers as $user_answer)
		{
			$answer = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_answer->get_answer_id());
			$answer_lines[] = $answer->get_title().' ('.Translation :: get('Score').': '.$user_answer->get_score().')';
		}
		$html[] = $this->display_answers($answer_lines);
		
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