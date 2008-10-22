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
			$this->init_question($question);
		}
	}
	
	function init_question($question) 
	{
		$type = $question['question']->get_question_type();
		$question_id = $question['question']->get_id();

		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question['question']->get_id());
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		
		while($clo_answer = $clo_answers->next_result())
		{
			$answers[] = array(
				'answer' => $dm->retrieve_learning_object($clo_answer->get_ref(), 'answer'),
			    'score' => $clo_answer->get_score()
			);
		}
		
		$this->add_question($question, $answers, $type);
	}
	
	function add_question($question, $answers) 
	{
		$question_id = $question['question']->get_id();
		$type = $question['question']->get_question_type();
		$descr = $question['question']->get_description();
		$weight = $question['weight'];
		
		switch($type)
		{
			case Question :: TYPE_OPEN:
				$this->addElement('html','Open question'.$descr.' Points:'.$weight.'<br/>');
				foreach($answers as $answer)
				{
					$this->addElement('text', $question_id, '');
				}
				break;
			case Question :: TYPE_FILL_IN_BLANKS:
				
				break;
			case Question :: TYPE_MATCHING:
				break;
			case Question :: TYPE_MULTIPLE_ANSWER:
				$this->addElement('html','Multiple answer'.$descr.' Points:'.$weight.'<br/>');
				foreach($answers as $answer)
				{
					$this->addElement('checkbox',$question_id, $answer['answer']->get_description());
				}
				break;
			case Question :: TYPE_MULTIPLE_CHOICE:
		$this->addElement('html','Multiple choice'.$descr.' Points:'.$weight.'<br/>');
				foreach($answers as $answer)
				{
					$this->addElement('radio',$question_id, $answer['answer']->get_description());
				}
				break;
			case Question :: TYPE_PERCENTAGE:
				break;
			case Question :: TYPE_SCORE:
				break;
			case Question :: TYPE_YES_NO:
				break;
		}
	}
}
?>