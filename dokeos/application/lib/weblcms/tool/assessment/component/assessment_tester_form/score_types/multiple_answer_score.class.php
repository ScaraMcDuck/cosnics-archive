<?php
require_once dirname(__FILE__).'/../score.class.php';

class MultipleAnswerScore extends Score
{
	
	function get_score()
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, parent :: get_answer()->get_id());
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		$clo_answer = $clo_answers->next_result();
		print_r($clo_answer);
		return $clo_answer->get_score();
	}
}
?>