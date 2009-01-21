<?php
require_once dirname(__FILE__).'/../score.class.php';

class MatchingScore extends Score
{
	
	function get_score()
	{
		//$correct = $this->get_link(parent :: get_answer()->get_id());
		$answers = $this->get_question()->get_options();
		$matches = $this->get_question()->get_matches();
		$correct = $answers[$this->get_answer_num()];
		$answer = $matches[$this->get_answer()];
		$match = $matches[$correct->get_match()];
		//if (parent :: get_user_answer()->get_extra() == $correct['answer']->get_id())
		if ($answer == $match)
		{
			//$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $this->get_user_answer()->get_answer_id());
			//$clos = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
			
			//$score = $clos->next_result()->get_score();
			return $correct->get_weight();
			//return $score;
		}
		else
		{
			return 0;
		}
	}
	
	/*function get_link($answer_id)
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $answer_id);
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		
		$clo_answer = $clo_answers->next_result();
		return array('answer' => $dm->retrieve_learning_object($clo_answer->get_ref(), 'answer'), 'score' => $clo_answer->get_score(), 'display_order' => $clo_answer->get_display_order());
	}*/
}
?>