<?php
/**
 * @package application.weblcms.tool.assessment.component
 */


require_once Path :: get_repository_path().'lib/learning_object/survey/survey.class.php';
require_once dirname(__FILE__).'/assessment_tester_form/assessment_score_calculator.class.php';
require_once dirname(__FILE__).'/assessment_tester_form/assessment_tester_display.class.php';
require_once dirname(__FILE__).'/../survey_invitation.class.php';

class AssessmentToolTesterComponent extends AssessmentToolComponent
{
	//private $questions;
	private $datamanager;
	
	private $pub;
	private $invitation;
	private $assessment;
	private $iid;
	private $pid;
	
	function run()
	{
		//$this->questions = null;
		$this->datamanager = WeblcmsDataManager :: get_instance();
		$showlcms = true;
		if (isset($_GET[Tool :: PARAM_PUBLICATION_ID]))
		{
			$this->pid = $_GET[Tool :: PARAM_PUBLICATION_ID];
			$this->pub = $this->datamanager->retrieve_learning_object_publication($this->pid);
			$this->assessment = $this->pub->get_learning_object();
			$url = $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, Tool :: PARAM_PUBLICATION_ID => $this->pid));
		}
		
		if (isset($_GET[AssessmentTool :: PARAM_INVITATION_ID]))
		{
			$this->iid = $_GET[AssessmentTool :: PARAM_INVITATION_ID];
			$showlcms = false;
			$condition = new EqualityCondition(SurveyInvitation :: PROPERTY_INVITATION_CODE, $this->iid);
			$this->invitation = $this->datamanager->retrieve_survey_invitations($condition)->next_result();
			$this->assessment = RepositoryDataManager :: get_instance()->retrieve_learning_object($this->invitation->get_survey_id());
			$url = $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, AssessmentTool :: PARAM_INVITATION_ID => $this->iid));
		}
		$visible = $this->is_visible();

		if (!$visible)
		{
			Display :: not_allowed();
			return;
		}
		
		if (isset($_GET['start']))
		{
			$_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE] = null;
			$_SESSION['formvalues'] = null;
		}
		
		if (isset($_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE]))
			$page = $_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE];
		else
			$page = 1;
			
		$form_display = new AssessmentTesterDisplay($this, $this->assessment);
		$show = $form_display->build_form($url, $page);
		if ($show)
			$this->show_form($form_display);

	}
	
	function show_form($form_display)
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new BreadCrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, AssessmentTool :: PARAM_PUBLICATION_ID => $pid, AssessmentTool :: PARAM_INVITATION_ID => $iid)), Translation :: get('TakeAssessment')));
		$this->display_header($trail);
			
		if ($showlcms)
		{
			$this->action_bar = $this->get_toolbar();
			echo $this->action_bar->as_html();
		}
		echo $form_display->as_html();
	
		$this->display_footer();
	}
	
	function is_visible()
	{
		$visible = false;

		if ($this->pub != null)
		{
			if ($this->pub->is_visible_for_target_users() && $this->is_allowed(VIEW_RIGHT))
			{
				$visible = true;
			}
		}
		if ($this->assessment->get_assessment_type() == Survey :: TYPE_SURVEY && $this->invitation != null)
		{
			if ($this->invitation->get_valid())
			{
				$visible = true;
			}
		}
		return $visible;
	}
	
	function redirect_to_score_calculator($values = null)
	{
		if ($values == null)
			$values = $_SESSION['formvalues'];
			
		$score_calculator = new AssessmentScoreCalculator();
		$user_assessment = $score_calculator->build_answers($values, $this->assessment, $this->datamanager, $this);
		
		WeblcmsDataManager :: get_instance()->create_user_assessment($user_assessment);
		$params = array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment->get_id());
		if ($this->invitation != null)
		{
			$this->invitation->set_valid(false);
			$this->datamanager->update_survey_invitation($this->invitation);
			$params[AssessmentTool::PARAM_INVITATION_ID] = $this->invitation->get_invitation_code();
		}	
		$this->redirect(null, null, false, $params);
	}
	
	function redirect_to_repoviewer()
	{
		$_SESSION['redirect_params'] = array(
			AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, 
			AssessmentTool :: PARAM_PUBLICATION_ID => $this->pid,
			AssessmentTool :: PARAM_INVITATION_ID => $this->iid
		);		
		$this->redirect(null, null, false, array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_REPOVIEWER, AssessmentTool :: PARAM_REPO_TYPES => array('document')));
	}
	
	static function calculate_score($user_assessment)
	{
		$score_calc = new AssessmentScoreCalculator();
		return $score_calc->calculate_score($user_assessment);
	}
}
?>