<?php
require_once dirname(__FILE__).'/../question_qti_import.class.php';

class ScoreQuestionQtiImport extends QuestionQtiImport
{
	
	function import_learning_object()
	{
		$data = $this->get_file_content_array();
		
		$question_type = Question :: TYPE_SCORE;
		$title = $data['title'];
		$descr = $data['itemBody']['sliderInteraction']['prompt'];
		$low = $data['itemBody']['sliderInteraction']['lowerBound'];
		$high = $data['itemBody']['sliderInteraction']['upperBound'];
		//echo 'Score question<br/>'.$question_type.'<br/>Title: '.$title.'<br/>Description: '.$descr;
		$question = parent :: create_question($title, $descr, $question_type);
		
		$low_answer = $this->create_answer($low);
		$high_answer = $this->create_answer($high);
		$this->create_complex_answer($question, $low_answer, $low);
		$this->create_complex_answer($question, $high_answer, $high);
		return $question->get_id();
	}
}
?>