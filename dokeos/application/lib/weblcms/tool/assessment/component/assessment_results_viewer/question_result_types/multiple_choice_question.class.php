<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class MultipleChoiceQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();
		
		$user_question = $this->get_user_question();
		$user_answers = $this->get_user_answers();
		$user_answer = $user_answers[0];
		
		if ($user_answer != null)
		{
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $user_answer->get_extra());
			$answer = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_answer->get_extra());
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $user_answer->get_answer_id());
			$clo_answer = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition)->next_result();
			$user_score_div = $clo_answer->get_score();
			$answer_lines[] = $answer->get_title().' ('.Translation :: get('Score').': '.$user_answer->get_score().')';
		
			$user_score = $user_answer->get_score();
		}
		else
		{
			$answer_lines[] = Translation :: get('NoAnswer');
			$user_score = 0;
		}
		
		$clo_question = $this->get_clo_question();
		$user_question_score = $user_score / $user_score_div * $clo_question->get_weight();
		
		$score_line = Translation :: get('Score').': '.$user_question_score.'/'.$clo_question->get_weight();
		$this->display_score($score_line);
		
		$this->display_answers($answer_lines);
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
			
		$this->display_feedback();
		$this->display_footer();
	}
	
	function display_survey()
	{
		$this->display_question_header();
		
		$user_question = $this->get_user_question();
		$user_answers = $this->get_user_answers();
		$user_answer = $user_answers[0];
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $user_answer->get_extra());
		$answer = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_answer->get_extra());

		$answer_lines[] = $answer->get_title();
		$this->display_answers($answer_lines);
		$this->display_footer();
	}
	
	function display_assignment()
	{
		$this->display_question();
	}
}
?>