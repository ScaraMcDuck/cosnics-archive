<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class DocumentQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();
		
		$user_question = $this->get_user_question();
		$user_answers = $this->get_user_answers();
		$user_answer = $user_answers[0];
		if ($user_answer != null)
		{
			$user_score = $user_answer->get_score();
			$lo_document = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_answer->get_extra(), 'document');
			$html_document = '<img src="'.Theme :: get_common_image_path().'learning_object/document.png" alt="">';
			$html_document .= ' <a href="'.htmlentities($lo_document->get_url()).'">'.$lo_document->get_filename()."</a> (size: ".$lo_document->get_filesize().") <br/>";
			$answer_lines[] = $html_document;
		}
		else
		{
			$user_score = 0;
			$answer_lines[] = Translation :: get('NoAnswer');
		}
		
		$score_line = Translation :: get('Score').': '.$user_score.'/'.$this->get_clo_question()->get_weight();
		$this->display_score($score_line);

		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_score_controls($this->get_clo_question()->get_weight());
		
		
		$this->display_answers($answer_lines);
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
			
		$this->display_feedback();
		$this->display_footer();
	}
	
	function display_survey()
	{
		$this->display_question_header();
		
		$user_question = $this->get_user_question();
		$user_answers = $this->get_user_answers();
		$user_answer = $user_answers[0];

		$lo_document = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_answer->get_extra(), 'document');
		$html_document = '<img src="'.Theme :: get_common_image_path().'learning_object/document.png" alt="">';
		$html_document .= ' <a href="'.htmlentities($lo_document->get_url()).'">'.$lo_document->get_filename()."</a> (size: ".$lo_document->get_filesize().") <br/>";
		$answer_lines[] = $html_document;
		$this->display_answers($answer_lines);
		$this->display_footer();
	}
	
	function display_assignment()
	{
		echo $this->display_question();
		//return implode('<br/>', $html);
	}
}
?>