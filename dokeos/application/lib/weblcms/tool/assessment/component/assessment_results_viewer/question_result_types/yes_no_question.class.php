<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class YesNoQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question();
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
			
		$this->display_feedback();
		$this->display_footer();
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