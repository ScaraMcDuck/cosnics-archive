<?php
require_once dirname(__FILE__).'/../score.class.php';

class MatchingScore extends Score
{
	
	function get_score()
	{
		//$question_id = parent :: get_question()->get_id();
		//$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question_id);
		/*$clo_answers = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
		while ($clo_answer = $clo_answers->next_result())
		{
			$answers[] = $this->get_link($clo_answer->get_ref());
		}
		$sorted_answers = $this->sort($answers);
		$index = parent :: get_user_answer()->get_extra();
		
		$user_answer = $sorted_answers[$index];
		$correct = $this->get_link(parent :: get_answer()->get_id());
		if ($user_answer['answer']->get_id() == $correct['answer']->get_id())
		{
			$answer_id = parent :: get_user_answer()->get_answer_id();
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $answer_id);
			$clos = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
			
			return $clos->next_result()->get_score();
		}*/
		$correct = $this->get_link(parent :: get_answer()->get_id());
		//$user_answer = RepositoryDataManager :: get_instance()->retrieve_learning_object(parent :: get_user_answer()->get_extra());
		echo parent :: get_user_answer()->get_extra().' == '.$correct['answer']->get_id();
		if (parent :: get_user_answer()->get_extra() == $correct['answer']->get_id())
		{
			$answer_id = parent :: get_user_answer()->get_answer_id();
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $answer_id);
			$clos = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
			
			return $clos->next_result()->get_score();
		}
		else
		{
			return 0;
		}
	}
	
	/*function sort($matches)
	{
		$num = count($matches);
		
		for ($pos = 0; $pos < $num; $pos++)
		{
			$largest = 0;
			$largest_pos = -1;
			for ($counter = $pos; $counter < $num; $counter++)
			{
				$display = $matches[$counter]['display_order'];
				if ($display > $largest)
				{
					$largest = $display;
					$largest_pos = $counter;
				}
			}
			//switchen
			if ($largest_pos != -1) 
			{
				$temp = $matches[$pos];
				$matches[$pos] = $matches[$largest_pos];
				$matches[$largest_pos] = $temp;
			}
		}
		
		return $matches;
	}*/
	
	function get_link($answer_id)
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $answer_id);
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		
		$clo_answer = $clo_answers->next_result();
		return array('answer' => $dm->retrieve_learning_object($clo_answer->get_ref(), 'answer'), 'score' => $clo_answer->get_score(), 'display_order' => $clo_answer->get_display_order());
	}
}
?>