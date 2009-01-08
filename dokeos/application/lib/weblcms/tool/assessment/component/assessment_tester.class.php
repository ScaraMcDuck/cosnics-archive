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
	
	function run()
	{
		$this->questions = null;
		$datamanager = WeblcmsDataManager :: get_instance();
		$showlcms = true;
		if (isset($_GET[Tool :: PARAM_PUBLICATION_ID]))
		{
			$pid = $_GET[Tool :: PARAM_PUBLICATION_ID];
			$pub = $datamanager->retrieve_learning_object_publication($pid);
			$assessment = $pub->get_learning_object();
			$url = $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, Tool :: PARAM_PUBLICATION_ID => $pid));
		}
		
		if (isset($_GET[AssessmentTool :: PARAM_INVITATION_ID]))
		{
			$iid = $_GET[AssessmentTool :: PARAM_INVITATION_ID];
			$showlcms = false;
			$condition = new EqualityCondition(SurveyInvitation :: PROPERTY_INVITATION_CODE, $iid);
			$invitation = $datamanager->retrieve_survey_invitations($condition)->next_result();
			$assessment = RepositoryDataManager :: get_instance()->retrieve_learning_object($invitation->get_survey_id());
			$url = $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, AssessmentTool :: PARAM_INVITATION_ID => $iid));
		}
		//work out visibility
		$visible = false;

		if ($pub != null)
		{
			if ($pub->is_visible_for_target_users() && $this->is_allowed(VIEW_RIGHT))
			{
				$visible = true;
			}
		}
		if ($assessment->get_assessment_type() == Survey :: TYPE_SURVEY && $invitation != null)
		{
			if ($invitation->get_valid())
			{
				$visible = true;
			}
		}

		if (!$visible)
		{
			Display :: not_allowed();
			return;
		}
		
		$tester_form = new AssessmentTesterForm($assessment, $url);
		if (!$tester_form->validate()) 
		{
			$trail = new BreadcrumbTrail();
			$this->display_header($trail);
			if ($showlcms)
			{
				$this->action_bar = $this->get_toolbar();
				echo $this->action_bar->as_html();
			}

			$formvalues = $_SESSION['formvalues'];
			if ($formvalues != null)
			{
				$_SESSION['formvalues'] = null;
				foreach ($formvalues as $id => $value)
				{
					$parts = split('_', $id);
					if ($parts[0] == 'repoviewer')
					{
						$control_id = $parts[1].'_'.$parts[2];
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
				
				$tester_form->setDefaults($formvalues);
			}
			echo $tester_form->toHtml();
			
			$this->display_footer();
		} 
		else
		{			
			$values = $tester_form->exportValues();
						
			if (isset($values['submit']))
			{
				$score_calculator = new AssessmentScoreCalculator();
				$user_assessment = $score_calculator->build_answers($tester_form, $assessment, $datamanager, $this);
				
				WeblcmsDataManager :: get_instance()->create_user_assessment($user_assessment);
				$params = array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment->get_id());
				if ($invitation != null)
				{
					$invitation->set_valid(false);
					$datamanager->update_survey_invitation($invitation);
					$params[AssessmentTool::PARAM_INVITATION_ID] = $invitation->get_invitation_code();
				}	
				$this->redirect(null, null, false, $params);
			}
			else
			{
				$_SESSION['formvalues'] = $values;
				$_SESSION['redirect_params'] = array(
					AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, 
					AssessmentTool :: PARAM_PUBLICATION_ID => $pid,
					AssessmentTool :: PARAM_INVITATION_ID => $iid
				);
				
				$this->redirect(null, null, false, array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_REPOVIEWER, AssessmentTool :: PARAM_REPO_TYPES => array('document')));
			}
		}
	}
}
?>