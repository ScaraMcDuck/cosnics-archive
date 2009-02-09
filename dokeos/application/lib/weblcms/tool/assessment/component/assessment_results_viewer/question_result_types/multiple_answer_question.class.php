<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class MultipleAnswerQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();
		
		$clo_question = $this->get_clo_question();
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
		
		$user_question_score = $user_score / $user_score_div * $clo_question->get_weight();
		
		$score_line = Translation :: get('Score').': '.$user_question_score.'/'.$clo_question->get_weight();
		//$this->display_score($score_line);
		
		foreach ($user_answers as $user_answer)
		{
			$answer = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_answer->get_answer_id());
			$answer_lines[] = $answer->get_title().' ('.Translation :: get('Score').': '.$user_answer->get_score().')';
		}
		/*$this->display_answers($answer_lines);
			
		$this->display_feedback();
		$this->display_score($score_line);
		
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
		
		$this->display_footer();*/
		
		$this->display_answers($answer_lines);
		$this->display_question_feedback();
		
		$this->display_score($score_line);
		$this->display_feedback();
		
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
		
		$this->display_footer();
	}
	
	function display_survey()
	{
		$this->display_question_header();
		
		$user_question = $this->get_user_question();
		$user_answers = $this->get_user_answers();
		$clo_answers = $this->get_clo_answers();

		foreach ($user_answers as $user_answer)
		{
			$answer = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_answer->get_answer_id());
			$answer_lines[] = $answer->get_title();
		}
		$this->display_answers($answer_lines);
		$this->display_footer();
	}
	
	function display_assignment()
	{
		$this->display_question();
	}
}
?>