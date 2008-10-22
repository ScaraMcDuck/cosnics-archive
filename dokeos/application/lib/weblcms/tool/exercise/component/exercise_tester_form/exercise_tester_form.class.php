<?php

require_once Path::get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/question_types/open_question.class.php';
require_once dirname(__FILE__).'/question_types/multiple_answer_question.class.php';
require_once dirname(__FILE__).'/question_types/multiple_choice_question.class.php';
require_once dirname(__FILE__).'/question_types/fill_in_blanks_question.class.php';
require_once dirname(__FILE__).'/question_types/matching_question.class.php';
require_once dirname(__FILE__).'/question_types/percentage_question.class.php';
require_once dirname(__FILE__).'/question_types/score_question.class.php';
require_once dirname(__FILE__).'/question_types/yes_no_question.class.php';

class ExerciseTesterForm extends FormValidator
{
	
	function ExerciseTesterForm($exercise)
	{
		parent :: __construct('publish', 'post');
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
			$question = $dm->retrieve_learning_object($clo_question->get_ref(), 'question');
			$type = $question->get_question_type();
			
			switch($type)
			{
			case Question :: TYPE_OPEN:
				$question_display = new OpenQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_FILL_IN_BLANKS:
				$question_display = new FillInBlanksQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_MATCHING:
				$question_display = new MatchingQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_MULTIPLE_ANSWER:
				$question_display = new MultipleAnswerQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_MULTIPLE_CHOICE:
				$question_display = new MultipleChoiceQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_PERCENTAGE:
				$question_display = new PercentageQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_SCORE:
				$question_display = new ScoreQuestionDisplay($clo_question);
				break;
			case Question :: TYPE_YES_NO:
				$question_display = new YesNoQuestionDisplay($clo_question);
				break;
			default:
				$question_display = null;
			}
			if (isset($question_display))
				$question_display->add_to($this);
		}
	}
	
	/*function init_questions($questions)
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
	}*/
	
	/*function init_question($question) 
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
	}*/
	
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
				$this->addElement('html','Percentage rating'.$descr.' Points:'.$weight.'<br/>');
		
				for ($i = 0; $i <= 100; $i++)
				{
					$scores[$i] = $i;
				}
				$this->addElement('select',$question_id, 'Score:',$scores);
				break;
			case Question :: TYPE_SCORE:
				$this->addElement('html','Point rating'.$descr.' Points:'.$weight.'<br/>');
				$minscore = $answers[0];
				$maxscore = $answers[1];
				
				$min = $minscore['score'];
				$max = $maxscore['score'];
			
				for ($i = $min; $i <= $max; $i++)
				{
					$scores[$i] = $i;
				}

				$this->addElement('select',$question_id, 'Score:',$scores);
				break;
			case Question :: TYPE_YES_NO:
				$this->addElement('html','Yes/No question'.$descr.' Points:'.$weight.'<br/>');
				foreach($answers as $answer)
				{
					$this->addElement('radio',$question_id, $answer['answer']->get_description());
				}
				break;
		}
	}
}
?>