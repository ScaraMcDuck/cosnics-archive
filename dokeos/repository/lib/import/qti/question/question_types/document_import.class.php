<?php
require_once dirname(__FILE__).'/../question_qti_import.class.php';

class DocumentQuestionQtiImport extends QuestionQtiImport
{
	
	function import_learning_object()
	{
		$data = $this->get_file_content_array();
		
		$question_type = Question :: TYPE_DOCUMENT;
		$title = $data['title'];
		$descr = $data['itemBody']['uploadInteraction']['prompt'];
		echo 'Document question<br/>'.$question_type.'<br/>Title: '.$title.'<br/>Description: '.$descr;
		$question = parent :: create_question($title, $descr, $question_type);
		return $question->get_id();
	}
	
}
?>