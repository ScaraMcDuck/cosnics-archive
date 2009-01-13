<?php
/**
 * @package application.weblcms.tool.assessment.component
 */
require_once dirname(__FILE__).'/assessment_tester_form/assessment_tester_form.class.php';
require_once dirname(__FILE__).'/assessment_tester_form/score.class.php';
require_once Path :: get_repository_path().'lib/learning_object/survey/survey.class.php';
require_once dirname(__FILE__).'/assessment_tester_form/assessment_score_calculator.class.php';
require_once dirname(__FILE__).'/../survey_invitation.class.php';

class AssessmentToolTesterComponent extends AssessmentToolComponent
{
	private $questions;
	private $datamanager;
	
	private $pub;
	private $invitation;
	private $assessment;
	private $iid;
	private $pid;
	
	function run()
	{
		$this->questions = null;
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
			
		$this->handle_form($url, $showlcms, $page);
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
	
	function handle_form($url, $showlcms, $page) 
	{
		//$_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE] = null;
		//$_SESSION['formvalues'] = null;
		$_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE] = $page;
		$qpp = $this->assessment->get_questions_per_page();
		$tester_form = new AssessmentTesterForm($this->assessment, $url, $page);
		
		if ($qpp > 0)
		{
			$questions = 0;
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->assessment->get_id());
			$clo_questions = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
			while ($clo_question = $clo_questions->next_result())
				$questions++;
			// subtract 1, last page may be less questions than questions per page
			if (($page - 1) * $qpp >= $questions)
			{
				//test done, redirect to scores
				$_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE] = null;
				$_SESSION['formvalues'] = null;
				$this->redirect_to_score_calculator($_SESSION['formvalues']);
			}
		}
		
		if (!$tester_form->validate()) 
		{
			$trail = new BreadcrumbTrail();
			$trail->add(new BreadCrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, AssessmentTool :: PARAM_PUBLICATION_ID => $pid, AssessmentTool :: PARAM_INVITATION_ID => $iid)), Translation :: get('TakeAssessment')));
			$this->display_header($trail);
			if ($showlcms)
			{
				$this->action_bar = $this->get_toolbar();
				echo $this->action_bar->as_html();
			}

			if ($qpp > 0)
			{
				echo '<h3>This is page: '.$page.'/'.$questions/$qpp.'</h3>';
			}
			$this->set_formvalues($tester_form);
			echo $tester_form->toHtml();
			
			$this->display_footer();
		} 
		else
		{	
			if ($tester_form != null)
				$values = $tester_form->exportValues();
						
			if (isset($values['submit']))
			{
				if ($qpp == 0) 
				{
					$this->redirect_to_score_calculator($values);
				}
				else
				{
					$old_values = $_SESSION['formvalues'];
					foreach ($old_values as $key => $value)
					{
						$new_values[$key] = $value;
					}
					foreach ($values as $key => $value)
					{
						$new_values[$key] = $value;
					}
					$_SESSION['formvalues'] = $new_values;
					$_POST = null;
					$this->handle_form($url, $showlcms, $page + 1);
				}
			}
			else
			{
				$old_values = $_SESSION['formvalues'];
				foreach ($old_values as $key => $value)
				{
					$new_values[$key] = $value;
				}
				foreach ($values as $key => $value)
				{
					$new_values[$key] = $value;
				}
				$_SESSION['formvalues'] = $new_values;
				$this->redirect_to_repoviewer();
			}
		}
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
	
	function set_formvalues($tester_form)
	{
		$formvalues = $_SESSION['formvalues'];
		if ($formvalues != null)
		{
			foreach ($formvalues as $id => $value)
			{
				$parts = split('_', $id);
				if ($parts[0] == 'repoviewer')
				{
					$control_id = $parts[1].'_'.$parts[2];
					
					if (isset($_GET['object']))
					{
						$objects = $_GET['object'];
						if (is_array($objects))
							$object = $objects[0];
						else
							$object = $objects;
							
						$formvalues[$control_id] = $objects;
						$doc = RepositoryDataManager :: get_instance()->retrieve_learning_object($objects);
						$formvalues[$control_id.'_name'] = $doc->get_title();
					}
				}
			}		
			$tester_form->setDefaults($formvalues);
		}
	}
	
	static function calculate_score($user_assessment)
	{
		$score_calc = new AssessmentScoreCalculator();
		return $score_calc->calculate_score($user_assessment);
	}
}
?>