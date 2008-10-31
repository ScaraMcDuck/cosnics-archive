<?php
require_once dirname(__FILE__).'/../score.class.php';

class MultipleChoiceScore extends Score
{
	
	function get_score()
	{
		$qid = parent :: get_question()->get_id();
		$dm = RepositoryDataManager :: get_instance();
		$question = $dm->retrieve_learning_object($qid, 'question');
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question->get_id());
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		
		$index = parent :: get_user_answer()->get_extra();
		$counter = 0;
		
		while ($counter < $index - 1)
		{
			$clo_answers->next_result();
			$counter++;
		}
		return $clo_answers->next_result()->get_score();
	}
}
?>