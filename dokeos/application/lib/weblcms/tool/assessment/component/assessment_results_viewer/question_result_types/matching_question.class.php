<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class MatchingQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();
		
		$rdm = RepositoryDataManager :: get_instance();
		$user_answers = parent :: get_user_answers();
		
		$clo_answers = parent :: get_clo_answers();
		foreach ($clo_answers as $clo_answer)
		{
			$total_div += $clo_answer->get_score();
		}
		
		foreach ($user_answers as $user_answer)
		{
			$answer = $rdm->retrieve_learning_object($user_answer->get_answer_id());
			$link = $rdm->retrieve_learning_object($user_answer->get_extra());
			$answers[] = array('answer' => $answer, 'link' => $link, 'score' => $user_answer->get_score());
			$total_score += $user_answer->get_score();
		}
		
		$score_line = Translation :: get('Score').': '.$total_score.'/'.$total_div;
		$this->display_score($score_line);
		
		foreach ($answers as $answer)
		{
			$line = $answer['answer']->get_title().' '.Translation :: get('linked to').' '.$answer['link']->get_title().' ('.Translation :: get('Score').': '.$answer['score'].')';
			if ($answer['score'] == 0)
			{
				$link = $this->get_link($answer['answer']->get_id());
				$line .= ' '.Translation :: get('Correct answer').': '.$link['answer']->get_title();
			}
			$answer_lines[] = $line;
		}
		$this->display_answers($answer_lines);
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

	function get_link($answer_id)
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $answer_id);
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		
		$clo_answer = $clo_answers->next_result();
		return array('answer' => $dm->retrieve_learning_object($clo_answer->get_ref(), 'answer'), 'score' => $clo_answer->get_score(), 'display_order' => $clo_answer->get_display_order());
	}
}
?>