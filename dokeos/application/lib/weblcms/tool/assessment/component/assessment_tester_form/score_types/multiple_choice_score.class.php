<?php
require_once dirname(__FILE__).'/../score.class.php';

class MultipleChoiceScore extends Score
{
	
	function get_score()
	{
		//$qid = parent :: get_question()->get_id();
		
		$dm = RepositoryDataManager :: get_instance();
		//$question = $dm->retrieve_learning_object($qid, 'question');
		$question = parent :: get_question();
		//print_r($question);
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question->get_id());
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		
		$index = parent :: get_user_answer()->get_extra();
		echo $index.'<br/>';
		$counter = 0;
		
		while ($counter < $index - 1)
		{
			$clo_answers->next_result();
			$counter++;
		}
		return $clo_answers->next_result()->get_score();
		/*$answer_id = parent :: get_user_answer()->get_extra();
		print_r(parent :: get_user_answer());
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $answer_id);
		return $dm->retrieve_complex_learning_object_item($condition)->next_result()->get_score();*/
	}
}
?>