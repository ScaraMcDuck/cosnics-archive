<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class FillInBlanksResultDisplay extends ResultDisplay
{
	function display_exercise()
	{
		$this->display_question_header();

		$results = parent :: get_results();
		$rdm = RepositoryDataManager :: get_instance();

		$question = parent :: get_question();
		$answers = $question->get_answers();
		
		foreach ($answers as $answer)
		{
			$total_div += $answer->get_weight();
		}
		
		foreach ($results as $result) 
		{
			$total_score += $result->get_score();
		}
		
		$total_score = $total_score / $total_div * $this->get_clo_question()->get_weight();
		$total_div = $this->get_clo_question()->get_weight();
		$score_line = Translation :: get('Score').': '.round($total_score).'/'.$total_div;
		//$this->display_score($score_line);
		
		foreach ($results as $result)
		{
			$answer_line = $result->get_answer();
			if($answer_line == '')
				$answer_line = Translation :: get('NoAnswer');
				
			if ($result->get_answer() == $answers[$result->get_answer_index()]->get_value())
				$answer_line = '<span style="color:green;">' . $answer_line . '</span>';
			else
				$answer_line = '<span style="color:red;">' . $answer_line . '</span>';
			
			$answer_line .= ' ('.Translation :: get('Score').': '.$result->get_score().')';
			$correct_answer_line = $answers[$result->get_answer_index()]->get_value() . ' <span style="color: navy; font-style: italic;">(' . $answers[$result->get_answer_index()]->get_comment() . ')</span>';
				
			$answer_lines[] = $answer_line;
			$correct_answer_lines[] = $correct_answer_line;
		}
		$this->display_answers($answer_lines, $correct_answer_lines);
		//$this->display_question_feedback();
		
		$this->display_score($score_line);
		$this->display_feedback();
		
		if ($this->get_edit_rights() == 1 && $feedback = Request :: get(AssessmentTool :: PARAM_ADD_FEEDBACK) == '1')
			$this->add_feedback_controls();
			
		$this->display_footer();
	}
	
	function display_survey()
	{
		$this->display_question_header();

		$results = parent :: get_results();
		$rdm = RepositoryDataManager :: get_instance();

		foreach ($results as $result)
		{
			$answer_lines[] = $result->get_extra();
		}
		$this->display_answers($answer_lines);
		$this->display_footer();
	}
	
	function display_assignment()
	{
		$this->display_question_header();

		$results = parent :: get_results();
		$rdm = RepositoryDataManager :: get_instance();

		$question = parent :: get_question();
		$answers = $question->get_answers();
		
		foreach ($answers as $answer)
		{
			$total_div += $answer->get_weight();
		}
		
		foreach ($results as $result) 
		{
			$total_score += $result->get_score();
		}
		
		$total_score = $total_score / $total_div * $this->get_clo_question()->get_weight();
		$total_div = $this->get_clo_question()->get_weight();
		$score_line = Translation :: get('Score').': '.round($total_score).'/'.$total_div;
		//$this->display_score($score_line);
		
		foreach ($results as $result)
		{
			$answer_line = $result->get_answer();
			if($answer_line == '')
				$answer_line = Translation :: get('NoAnswer');
				
			if ($result->get_answer() == $answers[$result->get_answer_index()]->get_value())
				$answer_line = '<span style="color:green;">' . $answer_line . '</span>';
			else
				$answer_line = '<span style="color:red;">' . $answer_line . '</span>';
			
			$answer_line .= ' ('.Translation :: get('Score').': '.$result->get_score().')';
			$correct_answer_line = $answers[$result->get_answer_index()]->get_value() . ' (' . $answers[$result->get_answer_index()]->get_comment() . ')';
				
			$answer_lines[] = $answer_line;
			$correct_answer_lines[] = $correct_answer_line;
		}
		
		$this->display_answers($answer_lines, $correct_answer_lines);
		//$this->display_question_feedback();
		
		$this->display_score($score_line);
		$this->display_feedback();
		
		if ($this->get_edit_rights() == 1 && $feedback = Request :: get(AssessmentTool :: PARAM_ADD_FEEDBACK) == '1')
			$this->add_feedback_controls();
			
		$this->display_footer();
	}
}
?>