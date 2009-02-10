<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class MatchingQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();
		
		$rdm = RepositoryDataManager :: get_instance();
		$results = parent :: get_results();
		
		$answers = parent :: get_question()->get_options();
		$matches = parent :: get_question()->get_matches();
		foreach ($answers as $answer)
		{
			$total_div += $answer->get_weight();
		}
		
		foreach ($results as $result)
		{
			$answer = $matches[$result->get_answer()];
			$ans_match = $answers[$result->get_answer_index()];
			$correct = $matches[$ans_match->get_match()];
			$answers_arr[] = array('answer' => $answer, 'match' => $ans_match->get_value(), 'correct' => $correct, 'score' => $result->get_score(), 'comment' => $ans_match->get_comment());
			$total_score += $result->get_score();
		}
		
		$total_score = $total_score / $total_div * $this->get_clo_question()->get_weight();
		$total_div = $this->get_clo_question()->get_weight();
		$score_line = Translation :: get('Score').': '.$total_score.'/'.$total_div;
		//$this->display_score($score_line);
		
		foreach ($answers_arr as $answer)
		{
			$answer_line = $answer['match'].' <b>'.Translation :: get('matches').'</b> ';
		
			if ($answer['answer'] != $answer['correct'])
			{
				$answer_line .= '<span style="color: red;">' . $answer['answer'] . '</span>';
			}
			else
			{
				$answer_line .= '<span style="color: green;">' . $answer['answer'] . '</span>';
			}
			
			$answer_line .= ' ('.Translation :: get('Score').': '.$answer['score'].')';
		
			$correct_answer_line = $answer['match'].' <b>'.Translation :: get('matches').'</b> '.$answer['correct'] . ' <span style="color: navy; font-style: italic;">(' . $answer['comment'] . ')</span>';
			
			$answer_lines[] = $answer_line;
			$correct_answer_lines[] = $correct_answer_line;
		}
		$this->display_answers($answer_lines, $correct_answer_lines);
		//$this->display_question_feedback();
		
		$this->display_score($score_line);
		$this->display_feedback();
		
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
		
		$this->display_footer();
	}
	
	function display_survey()
	{
		$this->display_question_header();
		$rdm = RepositoryDataManager :: get_instance();
		$user_answers = parent :: get_user_answers();

		foreach ($user_answers as $user_answer)
		{
			$answer = $rdm->retrieve_learning_object($user_answer->get_answer_id());
			$link = $rdm->retrieve_learning_object($user_answer->get_extra());
			$answers[] = array('answer' => $answer, 'link' => $link);
		}
		
		foreach ($answers as $answer)
		{
			$answer_line = $answer['answer']->get_title().' '.Translation :: get('LinkedTo').' '.$answer['link']->get_title();
			$answer_lines[] = $answer_line;
		}
		$this->display_answers($answer_lines);
		$this->display_footer();
	}
	
	function display_assignment()
	{
	$this->display_question_header();
		
		$rdm = RepositoryDataManager :: get_instance();
		$results = parent :: get_results();
		
		$answers = parent :: get_question()->get_options();
		$matches = parent :: get_question()->get_matches();
		foreach ($answers as $answer)
		{
			$total_div += $answer->get_weight();
		}
		
		foreach ($results as $result)
		{
			$answer = $matches[$result->get_answer()];
			$ans_match = $answers[$result->get_answer_index()];
			$correct = $matches[$ans_match->get_match()];
			$answers_arr[] = array('answer' => $answer, 'match' => $ans_match->get_value(), 'correct' => $correct, 'score' => $result->get_score(), 'comment' => $ans_match->get_comment());
			$total_score += $result->get_score();
		}
		
		$total_score = $total_score / $total_div * $this->get_clo_question()->get_weight();
		$total_div = $this->get_clo_question()->get_weight();
		$score_line = Translation :: get('Score').': '.$total_score.'/'.$total_div;
		//$this->display_score($score_line);
		
		foreach ($answers_arr as $answer)
		{
			$answer_line = $answer['match'].' <b>'.Translation :: get('matches').'</b> ';
		
			if ($answer['answer'] != $answer['correct'])
			{
				$answer_line .= '<span style="color: red;">' . $answer['answer'] . '</span>';
			}
			else
			{
				$answer_line .= '<span style="color: green;">' . $answer['answer'] . '</span>';
			}
			
			$answer_line .= ' ('.Translation :: get('Score').': '.$answer['score'].')';
			
			$correct_answer_line = $answer['match'].' <b>'.Translation :: get('matches').'</b> '.$answer['correct'] . ' <span style="color: navy; font-style: italic;">(' . $answer['comment'] . ')</span>';
			
			$answer_lines[] = $answer_line;
			$correct_answer_lines[] = $correct_answer_line;
		}
		
		$this->display_answers($answer_lines, $correct_answer_lines);
		//$this->display_question_feedback();
		
		$this->display_score($score_line);
		$this->display_feedback();
		
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
		
		$this->display_footer();
	}
}
?>