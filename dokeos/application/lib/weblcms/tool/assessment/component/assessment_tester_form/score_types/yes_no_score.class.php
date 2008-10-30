<?php
require_once dirname(__FILE__).'/../score.class.php';

class YesNoScore extends Score
{
	
	function get_score()
	{
		$qid = parent :: get_user_answer()->get_question_id();
		$dm = RepositoryDataManager :: get_instance();
		$question = $dm->retrieve_learning_object($qid, 'question');
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question->get_id());
		$clo_answers = $dm->retrieve_complex_learning_object_items();
		
		while ($clo_answer = $clo_answers->next_result())
		{
			$answers[] = $dm->retrieve_learning_object($clo_answer->get_ref(), 'answer');
		}
		
		$index = parent :: get_user_answer()->get_extra();
		return $answers[$index]->get_score();
	}
}
?>