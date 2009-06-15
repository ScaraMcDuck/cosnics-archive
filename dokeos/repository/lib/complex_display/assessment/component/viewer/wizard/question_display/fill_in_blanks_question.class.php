<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class FillInBlanksQuestionDisplay extends QuestionDisplay
{
	function add_question_form($formvalidator)
	{
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$answer_text = $question->get_answer_text();
		$answer_text = str_replace("\n", '<br />', $answer_text);
		
		$matches = array();
		preg_match_all('/\[[a-zA-Z0-9_-\s]*\]/', $answer_text, $matches);
		$matches = $matches[0];
		foreach($matches as $i => $match)
		{
			$name = $clo_question->get_id().'_'.$i;
			$element = $formvalidator->createElement('text', $name, '('.$i.')');
			$answer_text = str_replace($match, $element->toHtml(), $answer_text);
		}
		
		$formvalidator->addElement('static', null, null, $answer_text);
	}
	
	function get_instruction()
	{
		
	}
}
?>