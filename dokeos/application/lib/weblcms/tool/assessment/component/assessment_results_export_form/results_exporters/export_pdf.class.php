<?php
class ResultsPdfExport extends ResultsExport
 {
 	private $data;
 	
 	const PROPERTY_ASSESSMENT_TITLE = 'Title';
 	const PROPERTY_ASSESSMENT_DESCRIPTION = 'Description';
 	const PROPERTY_ASSESSMENT_TYPE = 'Type';
 	const PROPERTY_USERNAME = 'Username';
 	const PROPERTY_RESULT = 'Result';
 	const PROPERTY_DATE_TIME_TAKEN = 'Taken on';
 	const PROPERTY_QUESTION_TITLE = 'Question title';
 	const PROPERTY_QUESTION_DESCRIPTION = 'Question description';
 	const PROPERTY_WEIGHT = 'Weight';
 	const PROPERTY_ANSWER = 'Answer';
 	const PROPERTY_SCORE = 'Score';
 	const PROPERTY_FEEDBACK_TITLE = 'Feedback title';
 	const PROPERTY_FEEDBACK_DESCRIPTION = 'Feedback description';
 	
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
		$data = $this->export_assessment($assessment);
		$this->data[] = array('key' => 'Assessment', 'data' => array($data));
	}
	
 	function export_assessment($assessment)
	{
		$data[self :: PROPERTY_ASSESSMENT_TITLE] = $assessment->get_title();
		$data[self :: PROPERTY_ASSESSMENT_DESCRIPTION] = strip_tags($assessment->get_description());
		$data[self :: PROPERTY_ASSESSMENT_TYPE] = $assessment->get_assessment_type();
		return $data;
	}
	
	function export_user_assessment($user_assessment)
	{
		$data = $this->export_user($user_assessment->get_user_id());
		$data[self :: PROPERTY_RESULT] = $user_assessment->get_total_score();
		$data[self :: PROPERTY_DATE_TIME_TAKEN] = $user_assessment->get_date_time_taken();
		$this->data[] = array('key' => 'Result', 'data' => array($data));
		
		$condition = new EqualityCondition(UserQuestion :: PROPERTY_USER_ASSESSMENT_ID, $user_assessment->get_id());
		$user_questions = $this->wdm->retrieve_user_questions($condition);
		while ($user_question = $user_questions->next_result())
		{
			$this->export_question($user_question);
		}
	}
	
	function export_user($userid)
	{
		$user = UserDataManager :: get_instance()->retrieve_user($userid);
		$data[self :: PROPERTY_USERNAME] = $user->get_fullname();
		return $data;
	}
	
	function export_question($user_question)
	{
		$question = $this->rdm->retrieve_learning_object($user_question->get_question_id(), 'question');
		$data[self :: PROPERTY_QUESTION_TITLE] = $question->get_title();
		$data[self :: PROPERTY_QUESTION_DESCRIPTION] = strip_tags($question->get_description());
		$data[self :: PROPERTY_WEIGHT] = strip_tags($user_question->get_weight());
		$this->data[] = array('key' => 'Question', 'data' => array($data));
		 
		$condition = new EqualityCondition(UserAnswer :: PROPERTY_USER_QUESTION_ID, $user_question->get_id());
		$user_answers = $this->wdm->retrieve_user_answers($condition);
		while ($user_answer = $user_answers->next_result())
		{
			$question_data[] = $this->export_answer($user_answer);
		}
		$this->data[] = array('key' => 'Answers', 'data' => $question_data);
	}
	
	function export_feedback($feedback_id)
	{
		$feedback = $this->rdm->retrieve_learning_object($feedback_id, 'feedback');
		$data[self :: PROPERTY_FEEDBACK_TITLE] = $feedback->get_title();
		$data[self :: PROPERTY_FEEDBACK_DESCRIPTION] = strip_tags($feedback->get_description());
		$this->data[] = array('key' => 'Feedback', 'data' => array($data));
	}
	
	function export_answer($user_answer)
	{
		$data[self :: PROPERTY_ANSWER] = $user_answer->get_extra();
		$data[self :: PROPERTY_SCORE] = $user_answer->get_score();
		return $data;
	}
 }
?>