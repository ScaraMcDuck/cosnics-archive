<?php
class ResultsCsvExport extends ResultsExport
 {
 	private $currentrow;
 	private $data;
 	
 	const PROPERTY_ASSESSMENT_TITLE = 'assessment_title';
 	const PROPERTY_ASSESSMENT_DESCRIPTION = 'assessment_description';
 	const PROPERTY_ASSESSMENT_TYPE = 'assessment_type';
 	const PROPERTY_USERNAME = 'username';
 	const PROPERTY_RESULT = 'result';
 	const PROPERTY_DATE_TIME_TAKEN = 'date_time_taken';
 	const PROPERTY_QUESTION_TITLE = 'question_title';
 	const PROPERTY_QUESTION_DESCRIPTION = 'question_description';
 	const PROPERTY_WEIGHT = 'weight';
 	const PROPERTY_ANSWER = 'answer';
 	const PROPERTY_SCORE = 'score';
 	const PROPERTY_FEEDBACK_TITLE = 'feedback_title';
 	const PROPERTY_FEEDBACK_DESCRIPTION = 'feedback_description';
 	
 	function export_assessment_id($id)
	{
		$assessment = $this->rdm->retrieve_learning_object($id, 'assessment');
		$condition = new EqualityCondition(UserAssessment :: PROPERTY_ASSESSMENT_ID, $id);
		$user_assessments = $this->wdm->retrieve_user_assessments($condition);
		$this->export_header($assessment);
		while ($user_assessment = $user_assessments->next_result())
		{
			$this->export_user_assessment($user_assessment);
		}
		return $this->data;
	}
	
	function export_user_assessment_id($id)
	{
		$user_assessment = $this->wdm->retrieve_user_assessment($id);
		$assessment = $user_assessment->get_assessment();
		$this->export_header($assessment);
		$this->export_user_assessment($user_assessment);
		return $this->data;
	}
	
	function export_header($assessment)
	{
		$this->export_assessment($assessment);
		$this->data[] = $this->currentrow;
		$this->currentrow = array();
		$this->data[] = $this->currentrow;
		$this->currentrow = array(
			self :: PROPERTY_USERNAME,
	 		self :: PROPERTY_RESULT,
		 	self :: PROPERTY_DATE_TIME_TAKEN,
	 		self :: PROPERTY_QUESTION_TITLE,
	 		self :: PROPERTY_QUESTION_DESCRIPTION,
	 		self :: PROPERTY_WEIGHT,
	 		self :: PROPERTY_ANSWER,
	 		self :: PROPERTY_SCORE,
	 		self :: PROPERTY_FEEDBACK_TITLE,
	 		self :: PROPERTY_FEEDBACK_DESCRIPTION
		);
		$this->data[] = $this->currentrow;
		$this->currentrow = array();
	}
	
 	function export_assessment($assessment)
	{
		$this->currentrow[self :: PROPERTY_ASSESSMENT_TITLE] = $assessment->get_title();
		$this->currentrow[self :: PROPERTY_ASSESSMENT_DESCRIPTION] = strip_tags($assessment->get_description());
		$this->currentrow[self :: PROPERTY_ASSESSMENT_TYPE] = $assessment->get_assessment_type();
	}
	
	function empty_assessment_columns()
	{
		$this->currentrow[self :: PROPERTY_ASSESSMENT_TITLE] = ' ';
		$this->currentrow[self :: PROPERTY_ASSESSMENT_DESCRIPTION] = ' ';
		$this->currentrow[self :: PROPERTY_ASSESSMENT_TYPE] = ' ';
	}
	
	function export_user_assessment($user_assessment)
	{
		$this->export_user($user_assessment->get_user_id());
		$this->currentrow[self :: PROPERTY_RESULT] = $user_assessment->get_total_score();
		$this->currentrow[self :: PROPERTY_DATE_TIME_TAKEN] = $user_assessment->get_date_time_taken();
		
		$condition = new EqualityCondition(UserQuestion :: PROPERTY_USER_ASSESSMENT_ID, $user_assessment->get_id());
		$user_questions = $this->wdm->retrieve_user_questions($condition);
		while ($user_question = $user_questions->next_result())
		{
			$this->export_question($user_question);
			$this->empty_assessment_columns();
		}
	}
	
	function export_user($userid)
	{
		$user = UserDataManager :: get_instance()->retrieve_user($userid);
		$this->currentrow[self :: PROPERTY_USERNAME] = $user->get_fullname();
	}
	
	function export_question($user_question)
	{
		$question = $this->rdm->retrieve_learning_object($user_question->get_question_id(), 'question');
		$this->currentrow[self :: PROPERTY_QUESTION_TITLE] = $question->get_title();
		$this->currentrow[self :: PROPERTY_QUESTION_DESCRIPTION] = strip_tags($question->get_description());
		$this->currentrow[self :: PROPERTY_WEIGHT] = strip_tags($user_question->get_weight());
		 
		$condition = new EqualityCondition(UserAnswer :: PROPERTY_USER_QUESTION_ID, $user_question->get_id());
		$user_answers = $this->wdm->retrieve_user_answers($condition);
		while ($user_answer = $user_answers->next_result())
		{
			$this->export_answer($user_answer);
		}
		if ($user_question->get_feedback() != null && $user_question->get_feedback() > 0)
			$this->export_feedback($user_question->get_feedback());
		else
		{
			$this->currentrow[self :: PROPERTY_FEEDBACK_TITLE] = ' ';
			$this->currentrow[self :: PROPERTY_FEEDBACK_DESCRIPTION] = ' ';
		}
	}
	
	function export_feedback($feedback_id)
	{
		$feedback = $this->rdm->retrieve_learning_object($feedback_id, 'feedback');
		$this->currentrow[self :: PROPERTY_FEEDBACK_TITLE] = $feedback->get_title();
		$this->currentrow[self :: PROPERTY_FEEDBACK_DESCRIPTION] = strip_tags($feedback->get_description());
	}
	
	function export_answer($user_answer)
	{
		$this->currentrow[self :: PROPERTY_ANSWER] = $user_answer->get_extra();
		$this->currentrow[self :: PROPERTY_SCORE] = $user_answer->get_score();
		$this->data[] = $this->currentrow;
	}
 }
?>