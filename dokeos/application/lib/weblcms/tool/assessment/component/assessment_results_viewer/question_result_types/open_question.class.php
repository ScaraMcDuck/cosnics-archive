<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class OpenQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$html[] = $this->display_question_header();
		
		$user_question = $this->get_user_question();
		$user_answers = $this->get_user_answers();
		$user_answer = $user_answers[0];
		$user_score = $user_answer->get_score();
		
		$score_line = Translation :: get('Score').': '.$user_score.'/'.$user_question->get_weight();
		$html[] = $this->display_score($score_line);
		
		$answer_lines[] = $user_answer->get_extra();
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