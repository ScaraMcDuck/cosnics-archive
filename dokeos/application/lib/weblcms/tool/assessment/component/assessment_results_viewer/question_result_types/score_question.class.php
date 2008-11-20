<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class ScoreQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();
		
		$user_answers = parent :: get_user_answers();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $user_answers[0]->get_answer_id());
		$clo_answers = parent :: get_clo_answers();
		$low = $clo_answers[0];
		$high = $clo_answers[1];
		
		$score_line = Translation :: get('Your rating').': '.$user_answers[0]->get_extra().' ('.Translation :: get('from').' '.$low->get_score().' '.Translation :: get('to').' '.$high->get_score().')';
		$this->display_score($score_line);
		
		$this->display_answers();
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
			
		$this->display_feedback();
		$this->display_footer();
	}
	
	function display_survey()
	{
		$this->display_question_header();
		
		$user_answers = parent :: get_user_answers();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $user_answers[0]->get_answer_id());
		$clo_answers = parent :: get_clo_answers();
		$low = $clo_answers[0];
		$high = $clo_answers[1];
		
		$score_line = Translation :: get('Your rating').': '.$user_answers[0]->get_extra().' ('.Translation :: get('from').' '.$low->get_score().' '.Translation :: get('to').' '.$high->get_score().')';
		$this->display_score($score_line);
		
		$this->display_answers();
		$this->display_footer();
	}
	
	function display_assignment()
	{
		$this->display_question();
		//return implode('<br/>', $html);
	}
}
?>