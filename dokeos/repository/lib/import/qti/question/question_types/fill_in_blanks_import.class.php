<?php
require_once dirname(__FILE__).'/../question_qti_import.class.php';

class FillInBlanksQuestionQtiImport extends QuestionQtiImport
{
	
	function import_learning_object()
	{
		$data = $this->get_file_content_array();
		$title = $data['title'];
		
		//description may not be in prompt, but in a <p> tag, maybe even with an embedded blockquote
		$descr = $data['itemBody']['prompt'];
		if ($descr == null)
			$descr = $data['itemBody']['p'];

		if ($data['itemBody']['blockquote'] != null)
		{
			$descr .= $data['itemBody']['blockquote']['_content'];
		}
		$question = new FillInBlanksQuestion();
		$question->set_title($title);
		$question->set_description($description);
		
		$this->create_answers($data, $question);
		parent :: create_question($question);
		return $question->get_id();
	}
	
	function create_answers($data, $question)
	{
		$answers = $data['responseDeclaration'];
		
		foreach ($answers as $answer)
		{
			//$answer_list[$answer['identifier']] = $answer['correctResponse']['value'];
			$value = $answer['correctResponse']['value'];
			$weight = $answer['correctResponse']['mapping']['mapEntry']['mappedValue'];
			if ($weight = '')
				$weight = 1;
			//$answer_lo = $this->create_answer($answer['correctResponse']['value']);
			//$this->create_complex_answer($question, $answer_lo, 1);
			$fib_ans = new FillInBlanksQuestionAnswer($value, $weight);
			$question->add_answer($fib_ans);
		}
	}
}
?>