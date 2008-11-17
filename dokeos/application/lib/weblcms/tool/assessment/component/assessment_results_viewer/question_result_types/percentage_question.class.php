<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class PercentageQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();
		
		$user_answers = parent :: get_user_answers();
		
		$score_line = Translation :: get('Your rating').': '.$user_answers[0]->get_extra().'/100';
		$this->display_score($score_line);
		
		$this->display_answers();
		$this->add_feedback_controls();
		$this->display_footer();
		
		//return implode('<br/>', $html);
	}
	
	function display_survey()
	{
		
	}
	
	function display_assignment()
	{
		$this->display_question();
		//return implode('<br/>', $html);
	}
}
?>