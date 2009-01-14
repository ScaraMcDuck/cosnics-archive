<?php
require_once dirname(__FILE__) . '/../learning_path_learning_object_display.class.php';
require_once Path :: get_application_path().'lib/weblcms/tool/assessment/component/assessment_tester_form/assessment_tester_display.class.php';
require_once Path :: get_application_path().'lib/weblcms/tool/assessment/component/assessment_tester_form/assessment_score_calculator.class.php';
require_once Path :: get_application_path().'lib/weblcms/tool/assessment/component/assessment_results_viewer/results_viewer.class.php';
require_once Path :: get_repository_path().'lib/learning_object/assessment/assessment.class.php';
require_once Path :: get_repository_path().'lib/learning_object/survey/survey.class.php';

class AssessmentDisplay extends LearningPathLearningObjectDisplay
{
	private $datamanager;
	
	private $assessment;
	
	function display_learning_object($assessment)
	{
		$this->assessment = $assessment;
		$this->datamanager = WeblcmsDataManager :: get_instance();

		$url = $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, Tool :: PARAM_PUBLICATION_ID => $_GET[Tool :: PARAM_PUBLICATION_ID], LearningPathTool :: PARAM_LP_STEP => $_GET[LearningPathTool :: PARAM_LP_STEP]));

		if (!$this->get_parent()->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		if (!isset($_SESSION['started']))
		{
			$_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE] = null;
			$_SESSION['formvalues'] = null;
			$_SESSION['started'] = true;
		}
		
		if (isset($_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE]))
			$page = $_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE];
		else
			$page = 1;
			
		$form_display = new AssessmentTesterDisplay($this, $this->assessment);
		
		$result = $form_display->build_form($url, $page);
		if($result == 'form')
		{
			return $form_display->as_html();
		}
		else
		{
			return $result;
		}
	}
	
	function redirect_to_score_calculator($values = null)
	{
		if ($values == null)
			$values = $_SESSION['formvalues'];
			
		$score_calculator = new AssessmentScoreCalculator();
		$user_assessment = $score_calculator->build_answers($values, $this->assessment, $this->datamanager, $this->get_parent());
		
		WeblcmsDataManager :: get_instance()->create_user_assessment($user_assessment);
		$params = array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment->get_id());
		if ($this->invitation != null)
		{
			$this->invitation->set_valid(false);
			$this->datamanager->update_survey_invitation($this->invitation);
			$params[AssessmentTool::PARAM_INVITATION_ID] = $this->invitation->get_invitation_code();
		}	
		$results_form = ResultsViewer :: factory($user_assessment, false, null);
		$results_form->build();
		return $results_form->toHtml();
	}
	
	function redirect_to_repoviewer()
	{
		$_SESSION['redirect_params'] = array(
			Weblcms :: PARAM_TOOL => 'learning_path',
			LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, 
			Tool :: PARAM_PUBLICATION_ID => $_GET[Tool :: PARAM_PUBLICATION_ID],
			LearningPathTool :: PARAM_LP_STEP => $_GET[LearningPathTool :: PARAM_LP_STEP]
		);		
		$this->redirect(null, null, false, array(Weblcms :: PARAM_TOOL => 'assessment', AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_REPOVIEWER, AssessmentTool :: PARAM_REPO_TYPES => array('document')));
	}
	
	function get_url($params)
	{
		return $this->get_parent()->get_url($params);
	}
	
	function redirect($param1, $param2, $param3, $param4)
	{
		$this->get_parent()->redirect($param1, $param2, $param3, $param4);
	}
}
?>