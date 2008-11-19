<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class OpenQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();
		
		$user_question = $this->get_user_question();
		$user_answers = $this->get_user_answers();
		$user_answer = $user_answers[0];
		$user_score = $user_answer->get_score();
		
		$score_line = Translation :: get('Score').': '.$user_score.'/'.$user_question->get_weight();
		$this->display_score($score_line);
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_score_controls($this->get_clo_question()->get_weight());
		
		$answer_lines[] = $user_answer->get_extra();
		$this->display_answers($answer_lines);
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
			
		$this->display_feedback();
		$this->display_footer();
	}
	
	function display_survey()
	{
		
	}
	
	function display_assignment()
	{
		$this->display_question();
	}
}
?>