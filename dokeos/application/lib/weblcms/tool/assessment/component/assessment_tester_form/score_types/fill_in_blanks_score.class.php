<?php
require_once dirname(__FILE__).'/../score.class.php';

class FillInBlanksScore extends Score
{
	
	function get_score()
	{
		//$descr = parent :: get_answer()->get_title();
		//$answer = parent :: get_user_answer()->get_extra();
		
		//$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, parent:: get_answer()->get_id());
		//$clo_answers = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
		//$clo_answer = $clo_answers->next_result();
		$answers = $this->get_question()->get_answers();
		$descr = $answers[$this->get_answer_num()];
		$answer = $this->get_answer();
		if ($descr->get_value() == $answer)
		{
			return $descr->get_weight();
		} 
		else
		{
			return 0;
		}
	}
}
?>