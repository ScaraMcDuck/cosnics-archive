<?php
require_once dirname(__FILE__).'/../question_qti_import.class.php';

class MultipleAnswerQuestionQtiImport extends QuestionQtiImport
{
	
	function import_learning_object()
	{
		$data = $this->get_file_content_array();
		
		//$question_type = Question :: TYPE_MULTIPLE_ANSWER;
		$question = new MultipleChoiceQuestion();
		$question->set_answer_type('checkbox');
		$title = $data['title'];
		$descr = $data['itemBody']['choiceInteraction']['prompt'];
		$question->set_title($title);
		$question->set_description($descr);
		//echo 'Multiple answer question<br/>'.$question_type.'<br/>Title: '.$title.'<br/>Description: '.$descr;
		//$question = parent :: create_question($title, $descr, $question_type);
		
		$this->create_answers($data, $question);
		parent :: create_question($question);
		return $question->get_id();
	}
	
	function create_answers($data, $question_lo)
	{
		$answer_items = $data['itemBody']['choiceInteraction']['simpleChoice'];

		foreach ($answer_items as $answer)
		{
			$answers[$answer['identifier']]['title'] = $answer['_content'];
			$answers[$answer['identifier']]['score'] = 0;
		}
		
		$correct = $data['responseDeclaration']['mapping']['mapEntry'];
		foreach ($correct as $answer_score)
		{
			$answers[$answer_score['mapKey']]['score'] = $answer_score['mappedValue'];
		}
		
		//print_r($answers);
		
		foreach ($answers as $answer)
		{
			$opt = new MultipleChoiceQuestionOption($answer['title'], ($answer['score'] > 0), $answer['score']);
			$question_lo->add_option($opt);
			//$answer_lo = $this->create_answer($answer['title']);
			//$clo_answer = $this->create_complex_answer($question_lo, $answer_lo, $answer['score']);
		}
	}

}
?>