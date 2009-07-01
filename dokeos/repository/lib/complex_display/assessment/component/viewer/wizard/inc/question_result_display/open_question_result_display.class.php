<?php

require_once dirname(__FILE__).'/../question_result_display.class.php';

class OpenQuestionResultDisplay extends QuestionResultDisplay
{
	function display_question_result()
	{
		$answers = $this->get_answers();
		$html[] = $answers[0];
		$html[] = '<div class="warning-message">' . Translation :: get('NotYetRatedWarning') . '</div>';
		
		echo implode("\n", $html);
	}
	
	function add_borders()
	{
		return true;
	}
}
?>