<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class FillInBlanksQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$html[] = $this->display_question();
		return implode('<br/>', $html);
	}
	
	function display_survey()
	{
		
	}
	
	function display_assignment()
	{
		$html[] = $this->display_question();
		return implode('<br/>', $html);
	}
}
?>