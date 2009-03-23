<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class MultipleChoiceQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();
		
		$question = $this->get_question();
		$results = $this->get_results();
		
		if ($question->get_answer_type() == 'radio')
		{
			$result = $results[0];
			$answers = $question->get_options();
			if ($result != null)
			{
				$answer = $answers[$result->get_answer()];
				$answer_line = $answer->get_value();
			
				if($answer->is_correct())
				{
					$answer_line = '<span style="color: green">' . $answer_line . '</span>';
				}
				else
				{
					$answer_line = '<span style="color: red">' . $answer_line . '</span>';
				}
				
				//$answer_line = $result->get_answer() + 1 . '. ' . $answer_line;
				
				$answer_line .= ' ('.Translation :: get('Score').': '.$result->get_score().')';
				
				$answer_lines[] = $answer_line;
				$user_score = $result->get_score();
			}
			else
			{
				$answer_lines[] = Translation :: get('NoAnswer');
				$user_score = 0;
			}
		}
		else
		{
			$answers = $question->get_options();
			foreach($results as $result)
			{
				//dump($result);

				$answer = $answers[$result->get_answer_index()-1];
				$answer_line = $answer->get_value();
				
				if($answer->is_correct())
				{
					$answer_line = '<span style="color: green">' . $answer_line . '</span>';
				}
				else
				{
					$answer_line = '<span style="color: red">' . $answer_line . '</span>';
				}
				
				//$answer_line = $result->get_answer() + 1 . '. ' . $answer_line;
				$answer_line .= ' ('.Translation :: get('Score').': '.$result->get_score().')';
				
				$answer_lines[] = $answer_line;
				
				$user_score += $result->get_score();
			}
			if(count($results) == 0)
			{
				$answer_lines[] = Translation :: get('NoAnswer');
				$user_score = 0;
			}
		}
		
		$correct_lines = array();
		foreach($answers as $key => $answer)
		{
			if ($question->get_answer_type() == 'radio')
			{
				if ($answer->is_correct())
				{
					$user_score_div += $answer->get_weight();
				}
			}
			else
			{
				$user_score_div += $answer->get_weight();
			}
			
			if($answer->is_correct())
			{
				$correct_line = '<b>' . $answer->get_value() . '</b>';
				$correct_line .= ' <span style="color: navy; font-style: italic;">(' . $answer->get_comment() . ')</span>';
				$correct_lines[] = $correct_line;
			}
			else
			{
				//$correct_line = $answer->get_value();
			}
			
			
		}
		
		$clo_question = $this->get_clo_question();
		$user_question_score = $user_score / $user_score_div * $clo_question->get_weight();
		
		$score_line = Translation :: get('Score').': '.round($user_question_score).'/'.$clo_question->get_weight();
		
		$this->display_answers($answer_lines, $correct_lines);
		
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
		$user_answer = $user_answers[0];
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $user_answer->get_extra());
		$answer = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_answer->get_extra());

		$answer_lines[] = $answer->get_title();
		$this->display_answers($answer_lines);
		$this->display_footer();
	}
	
	function display_assignment()
	{
		$this->display_question_header();
		
		$question = $this->get_question();
		$results = $this->get_results();
		
		if ($question->get_answer_type() == 'radio')
		{
			$result = $results[0];
			$answers = $question->get_options();
			if ($result != null)
			{
				$answer = $answers[$result->get_answer()];
				$answer_line = $answer->get_value();
			
				if($answer->is_correct())
				{
					$answer_line = '<span style="color: green">' . $answer_line . '</span>';
				}
				else
				{
					$answer_line = '<span style="color: red">' . $answer_line . '</span>';
				}
				
				$answer_line .= ' ('.Translation :: get('Score').': '.$result->get_score().')';
				$answer_lines[] = $answer_line;
				$user_score = $result->get_score();
			}
			else
			{
				$answer_lines[] = Translation :: get('NoAnswer');
				$user_score = 0;
			}
		}
		else
		{
			$answers = $question->get_options();
			foreach($results as $result)
			{
				$answer = $answers[$result->get_answer()];
				$answer_line = $answer->get_value();
				
				if($answer->is_correct())
				{
					$answer_line = '<span style="color: green">' . $answer_line . '</span>';
				}
				else
				{
					$answer_line = '<span style="color: red">' . $answer_line . '</span>';
				}
				//$answer_line = $result->get_answer() + 1 . '. ' . $answer_line;
				$answer_line .= ' ('.Translation :: get('Score').': '.$result->get_score().')';
				$answer_lines[] = $answer_line;
				$user_score += $result->get_score();
			}
			if(count($results) == 0)
			{
				$answer_lines[] = Translation :: get('NoAnswer');
				$user_score = 0;
			}
		}
		
		$correct_lines = array();
		foreach($answers as $key => $answer)
		{
			if ($question->get_answer_type() == 'radio')
			{
				if ($answer->is_correct())
				{
					$user_score_div += $answer->get_weight();
				}
			}
			else
			{
				$user_score_div += $answer->get_weight();
			}
			
			if($answer->is_correct())
			{
				$correct_line = '<b>' . $answer->get_value() . '</b>';
			}
			else
			{
				$correct_line = $answer->get_value();
			}
			
			$correct_line .= ' <span style="color: navy; font-style: italic;">(' . $answer->get_comment() . ')</span>';
			
			$correct_lines[] = $correct_line;
		}
		
		$clo_question = $this->get_clo_question();
		$user_question_score = $user_score / $user_score_div * $clo_question->get_weight();
		
		$score_line = Translation :: get('Score').': '.round($user_question_score).'/'.$clo_question->get_weight();

		$this->display_answers($answer_lines, $correct_lines);
		
		$this->display_score($score_line);
		$this->display_feedback();
		
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
		
		$this->display_footer();
	}
}
?>