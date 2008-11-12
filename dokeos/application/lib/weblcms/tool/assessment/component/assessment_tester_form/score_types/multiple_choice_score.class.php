<?php
require_once dirname(__FILE__).'/../score.class.php';

class MultipleChoiceScore extends Score
{
	
	function get_score()
	{
		$dm = RepositoryDataManager :: get_instance();
		$answer_id = parent :: get_user_answer()->get_extra();
		print_r(parent :: get_user_answer());
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $answer_id);
		print_r($condition);
		
		return $dm->retrieve_complex_learning_object_items($condition)->next_result()->get_score();
	}
}
?>