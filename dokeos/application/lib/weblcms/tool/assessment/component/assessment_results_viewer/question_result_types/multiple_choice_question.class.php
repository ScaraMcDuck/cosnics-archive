<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class MultipleChoiceQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$html[] = $this->display_question();
		
		$user_question = $this->get_user_question();
		$user_answers = $this->get_user_answers();
		$user_answer = $user_answers[0];
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $user_answer->get_extra());
		$answer = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_answer->get_extra());
		$clo_answer = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition)->next_result();

		$user_score = $user_answer->get_score();
		$user_score_div = $clo_answer->get_score();
		
		$user_question_score = $user_score / $user_score_div * $user_question->get_weight();
		
		$html[] = '<div class="learning_object" style="">';
		$html[] = '<div class="title">';
		$html[] = 'Your answer: (Score: '.$user_question_score.'/'.$user_question->get_weight().')';
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = $answer->get_title().' Score: '.$user_answer->get_score();
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