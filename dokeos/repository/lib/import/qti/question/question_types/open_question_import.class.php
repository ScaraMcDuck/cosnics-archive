<?php
require_once dirname(__FILE__).'/../question_qti_import.class.php';

class OpenQuestionQtiImport extends QuestionQtiImport
{
	
	function import_learning_object()
	{
		$data = $this->get_file_content_array();
		
		//$question_type = Question :: TYPE_OPEN;
		$question = new OpenQuestion();
		$title = $data['title'];
		$descr = $data['itemBody']['extendedTextInteraction']['prompt'];
		$question->set_title($title);
		$question->set_description($description);
		//echo 'Open question<br/>'.$question_type.'<br/>Title: '.$title.'<br/>Description: '.$descr;
		$document = $data['itemBody']['uploadInteraction']['prompt'];
		if ($document == null)
			$question->set_question_type(OpenQuestion :: TYPE_OPEN);
		else
			$question->set_question_type(OpenQuestion :: TYPE_OPEN_WITH_DOCUMENT);
			
		//$question = parent :: create_question($title, $descr, $question_type);
		parent :: create_question($question);
		return $question->get_id();
	}
	

}
?>