<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class PercentageQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();
		
		$user_answers = parent :: get_user_answers();
		
		$score_line[] = Translation :: get('YourRating').': '.$user_answers[0]->get_extra().'/100';
		//$this->display_score($score_line);
		
		/*$this->display_answers($score_line);
			
		$this->display_feedback();
		$this->display_score();
		
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
		
		$this->display_footer();*/
		
		$this->display_answers($score_line);
		$this->display_question_feedback();
		
		$this->display_score();
		$this->display_feedback();
		
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
		
		$this->display_footer();
	}
	
	function display_survey()
	{
		$this->display_question_header();
		
		$user_answers = parent :: get_user_answers();
		
		$score_line = Translation :: get('YourRating').': '.$user_answers[0]->get_extra().'/100';
		$this->display_score();
		
		$this->display_answers($score_line);
		$this->display_footer();
	}
	
	function display_assignment()
	{
		$this->display_question();
	}
}
?>