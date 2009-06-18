<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class FillInBlanksQuestionDisplay extends QuestionDisplay
{
	function add_question_form()
	{
		$clo_question = $this->get_clo_question();
		$question = $this->get_question();
		$formvalidator = $this->get_formvalidator();
		$renderer = $this->get_renderer();

		$element_template = array();
		$element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
		$element_template[] = '<div class="clear">&nbsp;</div>';
		$element_template[] = '<div class="form_feedback"></div>';
		$element_template[] = '<div class="clear">&nbsp;</div>';
		$element_template[] = '</div>';
		$element_template = implode("\n", $element_template);

		$answer_text = $question->get_answer_text();
		$answer_text = nl2br($answer_text);

		$matches = array();
		preg_match_all('/\[[a-zA-Z0-9_-\s]*\]/', $answer_text, $matches);
		$matches = $matches[0];
		foreach($matches as $i => $match)
		{
			$name = $clo_question->get_id().'_'.$i;
			$element = $formvalidator->createElement('text', $name);
			$answer_text = str_replace($match, $element->toHtml(), $answer_text);
		}

		//$formvalidator->addElement('static', 'blanks', null, $answer_text);
		$formvalidator->addElement('html', $answer_text);
		$renderer->setElementTemplate($element_template, 'blanks');
	}

	function add_borders()
	{
		return true;
	}

	function get_instruction()
	{
		$instruction = array();
		$question = $this->get_question();

		if ($question->has_description())
		{
			$instruction[] = '<div class="splitter">';
			$instruction[] = Translation :: get('FillInTheBlanks');
			$instruction[] = '</div>';
		}
		else
		{
			$instruction = array();
		}

		return implode("\n", $instruction);
	}
}
?>