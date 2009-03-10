<?php
/**
 * @package application.weblcms.tool.assessment.component
 */


require_once Path :: get_repository_path().'lib/learning_object/survey/survey.class.php';
require_once dirname(__FILE__).'/assessment_tester_form/assessment_score_calculator.class.php';
require_once dirname(__FILE__).'/assessment_tester_form/assessment_tester_display.class.php';
require_once dirname(__FILE__).'/../survey_invitation.class.php';
require_once Path :: get_application_path().'lib/weblcms/trackers/weblcms_assessment_attempts_tracker.class.php';

class AssessmentToolTesterComponent extends AssessmentToolComponent
{
	private $datamanager;
	
	private $pub;
	private $invitation;
	private $assessment;
	private $iid;
	private $pid;
	private $showlcms;
	
	function run()
	{
		$this->datamanager = WeblcmsDataManager :: get_instance();
		$this->showlcms = true;
		if (isset($_GET[Tool :: PARAM_PUBLICATION_ID]))
		{
			$this->pid = $_GET[Tool :: PARAM_PUBLICATION_ID];
			$this->pub = $this->datamanager->retrieve_learning_object_publication($this->pid);
			$this->assessment = $this->pub->get_learning_object();
			$url = $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, Tool :: PARAM_PUBLICATION_ID => $this->pid));
		}
		
		if($this->assessment->get_assessment_type() == 'hotpotatoes')
		{
			$this->create_tracker();
			
			$this->display_header(new BreadcrumbTrail());
			
			$this->assessment->add_javascript($this->get_course_id());
			$path = $this->assessment->get_test_path();
			echo '<iframe src="' . $path . '" width="100%" height="600">
  				 <p>Your browser does not support iframes.</p>
				 </iframe>';
			//require_once $path;
			$this->display_footer();
			exit();
		}
		
		if (isset($_GET[AssessmentTool :: PARAM_INVITATION_ID]))
		{
			$this->iid = $_GET[AssessmentTool :: PARAM_INVITATION_ID];
			$this->showlcms = false;
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
			$this->create_tracker();
		}
		
		if (isset($_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE]))
			$page = $_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE];
		else
			$page = 1;
			
		$form_display = new AssessmentTesterDisplay($this, $this->assessment);
		$show = $form_display->build_form($url, $page);
		//dump($form_display);
		if($show == 'form')
		{
			//return $form_display->as_html();
			$this->show_form($form_display);
		}
		else
		{
			return $show;
		}
	}
	
	function create_tracker()
	{
		$args = array(
		'assessment_id' => $this->pid, 
		'user_id' => $this->get_user_id(), 
		'course_id' => $this->get_course_id(),
		'total_score' => 0
		);
		$tracker = new WeblcmsAssessmentAttemptsTracker();
		$tracker_id = $tracker->track($args);
		$_SESSION['assessment_tracker'] = $tracker_id;
	}
	
	function get_user_id()
	{
		if ($this->assessment->get_assessment_type() == Survey :: TYPE_SURVEY)
		{
			if ($this->assessment->get_anonymous() == true)
				return 0;
		}
		return parent :: get_user_id();
	}
	
	function show_form($form_display)
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new BreadCrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, AssessmentTool :: PARAM_PUBLICATION_ID => $pid, AssessmentTool :: PARAM_INVITATION_ID => $iid)), Translation :: get('TakeAssessment')));
		$this->display_header($trail);
			
		if ($this->showlcms)
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
		$score_calculator->build_answers($values, $this->assessment, $this->datamanager, $this);
		$uaid = $tracker_id = $_SESSION['assessment_tracker'];
		$_SESSION['assessment_tracker'] = null;
		//WeblcmsDataManager :: get_instance()->create_user_assessment($user_assessment);
		$params = array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $uaid);
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