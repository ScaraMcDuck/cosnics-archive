<?php
require_once dirname(__FILE__).'/../../../../../trackers/weblcms_question_attempts_tracker.class.php';

 class ResultsXmlExport extends ResultsExport
 {
	
	function export_publication_id($id)
	{
		$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($id);
		$assessment = $publication->get_learning_object();
		$condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $id);
		$track = new WeblcmsAssessmentAttemptsTracker();
		$user_assessments = $track->retrieve_tracker_items($condition);
		foreach ($user_assessments as $user_assessment)
		{
			$user_data['user_assessment'] = $this->export_user_assessment($user_assessment, $assessment->get_id());
		}
		$assessment_data = $this->export_assessment($assessment);
		$data['assessment_results'] = array('assessment' => $assessment_data, 'results' => $user_data);
		return $data;
	}
	
	function export_user_assessment_id($id)
	{
		$condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ID, $id);
		$track = new WeblcmsAssessmentAttemptsTracker();
		$user_assessments = $track->retrieve_tracker_items($condition);
		$user_assessment = $user_assessments[0];
		$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($user_assessment->get_assessment_id());
		$assessment = $publication->get_learning_object();
		$assessment_data = $this->export_assessment($assessment);
		$user_data['user_assessment'] = $this->export_user_assessment($user_assessment, $assessment->get_id());
		$data['assessment_results'] = array('assessment' => $assessment_data, 'results' => $user_data);
		return $data;
	}
 	
 	function export_assessment($assessment)
	{
		$data['id'] = $assessment->get_id();
		$data['title'] = htmlspecialchars($assessment->get_title());
		$data['description'] = htmlspecialchars($assessment->get_description());
		$data['assessment_type'] = htmlspecialchars($assessment->get_assessment_type());
		return $data;
	}
	
	function export_user_assessment($user_assessment, $assessment_id)
	{
		$data['id'] = $user_assessment->get_id();
		$data['assessment'] = $user_assessment->get_assessment_id();
		$data['user'] = $this->export_user($user_assessment->get_user_id());
		$data['total_score'] = $user_assessment->get_total_score();
		$data['date_time_taken'] = $user_assessment->get_date();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $assessment_id);

		$clo_questions = $this->rdm->retrieve_complex_learning_object_items($condition);
		while ($clo_question = $clo_questions->next_result())
		{
			$question_data[] = $this->export_question($clo_question, $user_assessment);
		}
		$data['questions'] = $question_data;
		return $data;
	}
	
	function export_user($userid)
	{
		$user = UserDataManager :: get_instance()->retrieve_user($userid);
		
		$data['id'] = $userid;
		$data['fullname'] = htmlspecialchars($user->get_fullname());
		return $data;
	}
	
	function export_question($clo_question, $user_assessment)
	{
		$question = $this->rdm->retrieve_learning_object($clo_question->get_ref());
		$data['id'] = $question->get_id();
		$data['title'] = $question->get_title();
		$data['weight'] = $clo_question->get_weight();

		$track = new WeblcmsQuestionAttemptsTracker();
		$condition_q = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $clo_question->get_id());
		$condition_a = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $user_assessment->get_id());
		$condition = new AndCondition(array($condition_q, $condition_a));
		$user_answers = $track->retrieve_tracker_items($condition);
		
		foreach ($user_answers as $user_answer)
		{
			if ($user_answer->get_feedback() != null && $user_answer->get_feedback() > 0)
				$data['feedback'] = $this->export_feedback($user_answer->get_feedback());
				
			$answer_data[] = $this->export_answer($user_answer);
		}
		$data['answers'] = $answer_data;
		return $data;
	}
	
	function export_feedback($feedback_id)
	{
		$feedback = $this->rdm->retrieve_learning_object($feedback_id, 'feedback');
		$data['id'] = $feedback->get_id();
		$data['title'] = htmlspecialchars($feedback->get_title());
		$data['description'] = htmlspecialchars($feedback->get_description());
		return $data;
	}
	
	function export_answer($user_answer)
	{
		$data['score'] = $user_answer->get_score();
		$data['answer'] = htmlspecialchars(unserialize($user_answer->get_answer()));
		return $data;
	}
 }
?>