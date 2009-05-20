<?php

require_once dirname(__FILE__) . '/../../../trackers/weblcms_question_attempts_tracker.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

class AssessmentToolQuestionFeedbackEditorComponent extends AssessmentToolComponent
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
			
			if(count($q_results) > 0)
			{
				$feedback_id = $q_results[0]->get_feedback();
				if($feedback_id)
				{
					$rdm = RepositoryDataManager :: get_instance();
					$feedback = $rdm->retrieve_learning_object($feedback_id);
					$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $feedback, 'edit', 'post', 
						$this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_EDIT_QUESTION_FEEDBACK, 
											 AssessmentTool :: PARAM_QUESTION_ATTEMPT => $question_id, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment)));
											 
					if($form->validate())
					{
						$succes = $form->update_learning_object();
					
						if($form->is_version())
						{	
							foreach($q_results as $result)
							{
								$result->set_feedback($feedback->get_latest_version()->get_id());
								$succes &= $result->update();
							}
						
						}
						
						if($succes)
						{
							$message = htmlentities(Translation :: get('QuestionFeedbackUpdated'));
						}
						else
						{
							$message = htmlentities(Translation :: get('QuestionFeedbackNotUpdated'));
						}
						
						$this->redirect($message, '', array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment));
					}
					else
					{
						$this->display_header(new BreadcrumbTrail(), true, 'courses assessment tool');
						$form->display();
						$this->display_footer();
					}
				}
			}
			
			
		}
	}

}
?>