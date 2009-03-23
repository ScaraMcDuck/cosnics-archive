<?php
require_once dirname(__FILE__).'/../question_qti_import.class.php';

class OpenQuestionQtiImport extends QuestionQtiImport
{
	
	function import_learning_object()
	{
		$data = $this->get_file_content_array();
		
		$question = new OpenQuestion();
		$title = $data['title'];
		//$descr = $data['itemBody']['extendedTextInteraction']['prompt'];
		$description = parent :: get_tag_content('prompt', 0); 
		$question->set_title($title);
		$question->set_description(parent :: import_images($description));
		//$document = $data['itemBody']['uploadInteraction']['prompt'];
		$document = parent :: get_tag_content('prompt', 1); 
		if ($document == null)
			$question->set_question_type(OpenQuestion :: TYPE_OPEN);
		else
			$question->set_question_type(OpenQuestion :: TYPE_OPEN_WITH_DOCUMENT);
			
		parent :: create_question($question);
		return $question->get_id();
	}
	

}
?>