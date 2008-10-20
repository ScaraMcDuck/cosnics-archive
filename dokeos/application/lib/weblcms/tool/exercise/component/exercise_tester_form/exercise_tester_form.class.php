<?php

require_once Path::get_library_path().'html/formvalidator/FormValidator.class.php';

class ExerciseTesterForm extends FormValidator
{
	
	function ExerciseTesterForm($exercise)
	{
		$this->initialize($exercise);
	}
	
	function initialize($exercise) 
	{
		$exercise_id = $exercise->get_id();
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $exercise_id);
		$clo_questions = $dm->retrieve_complex_learning_object_items($condition);
		
		while($clo_question = $clo_questions->next_result())
		{
			$questions[] = array(
				'question' => $dm->retrieve_learning_object($clo_question->get_ref(), 'question'),
			    'weight' => $clo_question->get_weight()
			);
		}
		
		$this->init_questions($questions);
	}
	
	function init_questions($questions)
	{
		$num_questions = count($questions);
		if ($num_questions == 0)	
		{
			echo 'No questions yet';
		} else {
			echo $num_questions.' questions';
		}
		foreach($questions as $question)
		{
			$type = $question['question']->get_question_type();
			$weight = $question['weight'];
			//$this->addElement('html','Question '.$type.' w:'.$weight.'<br/>');
			echo '<br/>Question: '.$type;
		}
	}
}
?>