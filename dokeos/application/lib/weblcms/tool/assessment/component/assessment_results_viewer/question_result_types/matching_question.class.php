<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class MatchingQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$html[] = $this->display_question();
		
		$rdm = RepositoryDataManager :: get_instance();
		$user_answers = parent :: get_user_answers();
		foreach ($user_answers as $user_answer)
		{
			$answer = $rdm->retrieve_learning_object($user_answer->get_answer_id());
			$link = $rdm->retrieve_learning_object($user_answer->get_extra());
			$answers[] = array('answer' => $answer, 'link' => $link, 'score' => $user_answer->get_score());
		}
		
		$html[] = '<div class="learning_object" style="">';
		$html[] = '<div class="title">';
		$html[] = 'Your answer(s): (Score: '.$total_score.'/'.$total_div.')';
		$html[] = '</div>';
		$html[] = '<div class="description">';
		foreach ($answers as $answer)
		{
			$html[] = $answer['answer']->get_title().': '.$answer['link']->get_title().' (Score: '.$answer['score'].')';
		}
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode('<br/>', $html);
	}
	
	function display_survey()
	{
		
	}
	
	function display_assignment()
	{
		$html[] = $this->display_question();
		return implode('<br/>', $html);
	}

}
?>