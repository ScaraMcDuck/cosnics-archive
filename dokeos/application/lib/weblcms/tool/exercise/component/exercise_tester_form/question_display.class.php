<?php

abstract class QuestionDisplay 
{
	private $clo_question;
	
	function QuestionDisplay($clo_question)
	{
		$this->clo_question = $clo_question;
	}
	
	function get_clo_question()
	{
		return $this->clo_question;
	}
	
	abstract function add_to($formvalidator);
	
	function get_answers($question_id)
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question_id);
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		
		while($clo_answer = $clo_answers->next_result())
		{
			$answers[] = array(
				'answer' => $dm->retrieve_learning_object($clo_answer->get_ref(), 'answer'),
			    'score' => $clo_answer->get_score()
			);
		}
		
		return $answers;
	}
}
?>