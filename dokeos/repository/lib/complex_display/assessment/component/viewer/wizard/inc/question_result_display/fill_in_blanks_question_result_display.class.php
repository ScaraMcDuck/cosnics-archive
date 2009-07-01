<?php

require_once dirname(__FILE__).'/../question_result_display.class.php';

class FillInBlanksQuestionResultDisplay extends QuestionResultDisplay
{

	function add_borders()
	{
		return true;
	}
	
}
?>