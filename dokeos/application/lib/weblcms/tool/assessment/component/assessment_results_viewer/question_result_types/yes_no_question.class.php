<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class YesNoQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question();
		$this->add_feedback_controls();
		$this->display_footer();
		//return implode('<br/>', $html);
	}
	
	function display_survey()
	{
		
	}
	
	function display_assignment()
	{
		$this->display_question();
		//return implode('<br/>', $html);
	}
}
?>