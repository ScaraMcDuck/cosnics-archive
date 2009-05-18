<?php

require_once dirname(__FILE__) . '/../../../trackers/weblcms_question_attempts_tracker.class.php';

class AssessmentToolQuestionFeedbackDeleterComponent extends AssessmentToolComponent
{
	function run()
	{
		if($this->is_allowed(DELETE_RIGHT))
		{ 
			$question_id = Request :: get(AssessmentTool :: PARAM_QUESTION_ATTEMPT);
			$user_assessment = Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT);
			
			$track = new WeblcmsQuestionAttemptsTracker();
			$condition_ass = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $user_assessment);
			$condition_question = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_QUESTION_ID, $question_id);
			$condition = new AndCondition(array($condition_ass, $condition_question));
			$q_results = $track->retrieve_tracker_items($condition);
			
			$succes = true;
			
			foreach($q_results as $result)
			{
				$result->set_feedback(0);
				$succes &= $result->update();
			}
			
			if($succes)
			{
				$message = htmlentities(Translation :: get('QuestionFeedbackDeleted'));
			}
			else
			{
				$message = htmlentities(Translation :: get('QuestionFeedbackNotDeleted'));
			}
			
			$this->redirect($message, '', array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment));
		}
	}

}
?>