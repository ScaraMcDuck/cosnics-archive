<?php
require_once dirname(__FILE__).'/../question_qti_import.class.php';

class FillInBlanksQuestionQtiImport extends QuestionQtiImport
{
	
	function import_learning_object()
	{
		$data = $this->get_file_content_array();
		
		$question_type = Question :: TYPE_FILL_IN_BLANKS;
		$title = $data['title'];
		
		//description may not be in prompt, but in a <p> tag, maybe even with an embedded blockquote
		$descr = $data['itemBody']['prompt'];
		if ($descr == null)
			$descr = $data['itemBody']['p'];

		if ($data['itemBody']['blockquote'] != null)
		{
			$descr .= $data['itemBody']['blockquote']['_content'];
		}
		echo 'Fill in blanks question<br/>'.$question_type.'<br/>Title: '.$title.'<br/>Description: '.$descr;
		$question = parent :: create_question($title, $descr, $question_type);
		
		$this->create_answers($data, $question);
		return $question->get_id();
	}
	
	function create_answers($data, $question)
	{
		$answers = $data['responseDeclaration'];
		
		foreach ($answers as $answer)
		{
			//$answer_list[$answer['identifier']] = $answer['correctResponse']['value'];
			$answer_lo = $this->create_answer($answer['correctResponse']['value']);
			$this->create_complex_answer($question, $answer_lo, 1);
		}
	}
}
?>