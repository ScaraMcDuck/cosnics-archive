<?php
require_once dirname(__FILE__).'/../score.class.php';

class FillInBlanksScore extends Score
{
	
	function get_score()
	{
		$descr = parent :: get_answer()->get_title();
		$answer = parent :: get_user_answer()->get_extra();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, parent:: get_answer()->get_id());
		$clo_answers = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
		$clo_answer = $clo_answers->next_result();
		if ($descr == $answer)
		{
			return $clo_answer->get_score();
		} 
		else
		{
			return 0;
		}
	}
}
?>