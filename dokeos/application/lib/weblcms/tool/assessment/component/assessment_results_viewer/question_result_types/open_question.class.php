<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class OpenQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();
		
		//$user_question = $this->get_user_question();
		$results = $this->get_results();
		$question = $this->get_question();
		$type = $question->get_question_type();
		
		switch ($type)
		{
			case OpenQuestion :: TYPE_OPEN:
				$result = $results[0];
				$user_score = $result->get_score();
		
				$score_line = Translation :: get('Score').': '.$user_score.'/'.parent :: get_clo_question()->get_weight();
				$this->display_score($score_line);
				if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
					$this->add_score_controls($this->get_clo_question()->get_weight());
		
				$answer_lines[] = $result->get_answer();
				break;
			case OpenQuestion :: TYPE_DOCUMENT:
				$result = $results[0];
				$user_score = $result->get_score();
				if ($result->get_answer() != null)
				{
					$lo_document = RepositoryDataManager :: get_instance()->retrieve_learning_object($result->get_answer(), 'document');
					$html_document = '<img src="'.Theme :: get_common_image_path().'learning_object/document.png" alt="">';
					$html_document .= ' <a href="'.htmlentities($lo_document->get_url()).'">'.$lo_document->get_filename()."</a> (size: ".$lo_document->get_filesize().") <br/>";
					$answer_lines[] = $html_document;
				}
				else
					$answer_lines[] = Translation :: get('NoDocument');
				break;
			case OpenQuestion :: TYPE_OPEN_WITH_DOCUMENT:
				$result = $results[0];
				$user_score = $result->get_score();
		
				$score_line = Translation :: get('Score').': '.$user_score.'/'.parent :: get_clo_question()->get_weight();
				$this->display_score($score_line);
				if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
					$this->add_score_controls($this->get_clo_question()->get_weight());
		
				$answer_lines[] = $result->get_answer();
				$result = $results[1];
				$user_score = $result->get_score();
				if ($result->get_answer() != null)
				{
					$lo_document = RepositoryDataManager :: get_instance()->retrieve_learning_object($result->get_answer(), 'document');
					$html_document = '<img src="'.Theme :: get_common_image_path().'learning_object/document.png" alt="">';
					$html_document .= ' <a href="'.htmlentities($lo_document->get_url()).'">'.$lo_document->get_filename()."</a> (size: ".$lo_document->get_filesize().") <br/>";
					$answer_lines[] = $html_document;
				}
				else
					$answer_lines[] = Translation :: get('NoDocument');
				break;
			default:
				break;
		}
		
		$this->display_answers($answer_lines, false);
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
		$answer_lines[] = $user_answer->get_extra();
		$this->display_answers($answer_lines);

		$this->display_footer();
	}
	
	function display_assignment()
	{
		$this->display_question();
	}
}
?>