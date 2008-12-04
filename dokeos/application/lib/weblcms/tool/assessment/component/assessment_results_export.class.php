<?php
require_once dirname(__FILE__).'/assessment_results_export_form/results_export_form.class.php';

class AssessmentToolResultsExportComponent extends AssessmentToolComponent
{
	
	private $rdm;
	private $wdm;
	
	function run()
	{
		if (!$this->is_allowed(VIEW_RIGHT) || !$this->is_allowed(EDIT_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		$toolbar = $this->get_toolbar();
		
		if (isset($_GET[AssessmentTool :: PARAM_USER_ASSESSMENT]))
		{
			$id = $_GET[AssessmentTool :: PARAM_USER_ASSESSMENT];
			$type = 'user_assessment';
			$export_form = new AssessmentResultsExportForm($this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $id)));
		} 
		else
		{
			$id = $_GET[AssessmentTool :: PARAM_ASSESSMENT];
			$type = 'assessment';
			$export_form = new AssessmentResultsExportForm($this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $id)));
		}
		
		if ($export_form->validate())
		{
			$values = $export_form->exportValues();
			$filetype = $values['filetype'];
			$this->export($type, $id, $filetype);
		}
		else
		{
			$this->display_header($trail);
			echo $toolbar->as_html();
			echo $export_form->toHtml();
			$this->display_footer();
		}
	}
	
	function export($type, $id, $filetype)
	{
		$exporter = Export :: factory($filetype, 'export_'.$type.$id);
		
		$this->rdm = RepositoryDataManager :: get_instance();
		$this->wdm = WeblcmsDataManager :: get_instance();
		
		if ($type == 'assessment')
		{
			$assessment = $this->rdm->retrieve_learning_object($id, 'assessment');
			$condition = new EqualityCondition(UserAssessment :: PROPERTY_ASSESSMENT_ID, $id);
			$user_assessments = $this->wdm->retrieve_user_assessments($condition);
			
			while ($user_assessment = $user_assessments->next_result())
			{
				$user_data['user_assessment'] = $this->export_user_assessment($user_assessment);
			}
			$assessment_data = $this->export_assessment($assessment);
			$data['assessment_results'] = array('assessment' => $assessment_data, 'results' => $user_data);
		}
		else
		{
			$user_assessment = $this->wdm->retrieve_user_assessment($id);
			$assessment = $user_assessment->get_assessment();
			
			$assessment_data = $this->export_assessment($assessment);
			$user_data['user_assessment'] = $this->export_user_assessment($user_assessment);
			$data['assessment_results'] = array('assessment' => $assessment_data, 'results' => $user_data);
		}
		
		$exporter->write_to_file($data);
	}
	
	function export_assessment($assessment)
	{
		$data['id'] = $assessment->get_id();
		$data['title'] = htmlspecialchars($assessment->get_title());
		$data['description'] = htmlspecialchars($assessment->get_description());
		$data['assessment_type'] = htmlspecialchars($assessment->get_assessment_type());
		return $data;
	}
	
	function export_user_assessment($user_assessment)
	{
		$data['id'] = $user_assessment->get_id();
		$data['assessment'] = $user_assessment->get_assessment_id();
		$data['user'] = $this->export_user($user_assessment->get_user_id());
		$data['total_score'] = $user_assessment->get_total_score();
		$data['date_time_taken'] = $user_assessment->get_date_time_taken();
		
		$condition = new EqualityCondition(UserQuestion :: PROPERTY_USER_ASSESSMENT_ID, $user_assessment->get_id());
		$user_questions = $this->wdm->retrieve_user_questions($condition);
		while ($user_question = $user_questions->next_result())
		{
			$question_data[] = $this->export_question($user_question);
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
	
	function export_question($user_question)
	{
		$data['id'] = $user_question->get_id();
		$data['question_id'] = $user_question->get_question_id();
		if ($user_question->get_feedback() != null && $user_question->get_feedback() > 0)
			$data['feedback'] = $this->export_feedback($user_question->get_feedback());

		$condition = new EqualityCondition(UserAnswer :: PROPERTY_USER_QUESTION_ID, $user_question->get_id());
		$user_answers = $this->wdm->retrieve_user_answers($condition);
		while ($user_answer = $user_answers->next_result())
		{
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
		$data['id'] = $user_answer->get_id();
		$data['answer_id'] = $user_answer->get_answer_id();
		$data['score'] = $user_answer->get_score();
		$data['extra'] = htmlspecialchars($user_answer->get_extra());
		return $data;
	}
}
?>