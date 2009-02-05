<?php
require_once dirname(__FILE__).'/../question_qti_import.class.php';

class ScoreQuestionQtiImport extends QuestionQtiImport
{
	
	function import_learning_object()
	{
		$data = $this->get_file_content_array();
		
		$title = $data['title'];
		$descr = $data['itemBody']['sliderInteraction']['prompt'];
		$low = $data['itemBody']['sliderInteraction']['lowerBound'];
		$high = $data['itemBody']['sliderInteraction']['upperBound'];
		$question = new RatingQuestion();
		$question->set_title($title);
		$question->set_description($description);
		$question->set_high($high);
		$question->set_low($low);
		parent :: create_question($question);
		return $question->get_id();
	}
}
?>