<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class FillInBlanksQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();

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
		
		$total_score = $total_score / $total_div * $this->get_clo_question()->get_weight();
		$total_div = $this->get_clo_question()->get_weight();
		$score_line = Translation :: get('Score').': '.$total_score.'/'.$total_div;
		$this->display_score($score_line);
		
		foreach ($user_answers as $user_answer)
		{
			$line = $user_answer->get_extra().' ('.Translation :: get('Score').': '.$user_answer->get_score().')';
			if ($user_answer->get_score() == 0)
				$line .= ' '.Translation :: get('Correct answer').': '.$rdm->retrieve_learning_object($user_answer->get_answer_id())->get_title();
				
			$answer_lines[] = $line;
		}
		$this->display_answers($answer_lines);
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
		
		$this->display_feedback();
		$this->display_footer();
	}
	
	function display_survey()
	{
		$this->display_question_header();

		$user_answers = parent :: get_user_answers();
		$rdm = RepositoryDataManager :: get_instance();

		foreach ($user_answers as $user_answer)
		{
			$answer_lines[] = $user_answer->get_extra();
		}
		$this->display_answers($answer_lines);
		$this->display_footer();
	}
	
	function display_assignment()
	{
		$this->display_question();
		//return implode('<br/>', $html);
	}
}
?>