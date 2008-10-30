<?php
/**
 * @package application.weblcms.tool.assessment.component
 */
require_once dirname(__FILE__).'/assessment_tester_form/assessment_tester_form.class.php';

class AssessmentToolTesterComponent extends AssessmentToolComponent
{
	function run()
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		
		$pid = $_GET[Tool :: PARAM_PUBLICATION_ID];
		$pub = $datamanager->retrieve_learning_object_publication($pid);
		$visible = !$pub->is_hidden() && $pub->is_visible_for_target_users();
		
		if (!$this->is_allowed(VIEW_RIGHT) || !$visible)
		{
			Display :: display_not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		$assessment = $pub->get_learning_object();
		
		$tester_form = new AssessmentTesterForm($assessment, $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, Tool :: PARAM_PUBLICATION_ID => $pid)));
		if (!$tester_form->validate()) 
		{
			$this->display_header($trail);
			echo $tester_form->toHtml();
			$this->display_footer();
		} 
		else
		{
			$uaid = $this->build_answers($tester_form, $assessment, $datamanager);
			$params = array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $uaid);
			$this->redirect(null, null, false, $params);
		}
	}
	
	function build_answers($tester_form, $assessment, $datamanager)
	{
		$values = $tester_form->exportValues();
		$user_assessment = new UserAssessment();
		$user_assessment->set_assessment_id($assessment->get_id());
		$user_assessment->set_user_id(1);
		$id = $datamanager->get_next_user_assessment_id();
		$user_assessment->set_id($id);
		
		if($datamanager->create_user_assessment($user_assessment)) {
			foreach($values as $key => $value)
			{
				$this->add_user_answer($datamanager, $user_assessment, $key, $value);
			}
		}
		return $id;
	}
	
	function add_user_answer($datamanager, $user_assessment, $key, $value)
	{
		if ($key != 'submit') {
			$answer = new UserAnswer();
			$answer->set_id($datamanager->get_next_user_answer_id());
			$answer->set_user_test_id($user_assessment->get_id());
			$parts = split("_", $key);
			$answer->set_question_id($parts[0]);
			$answer->set_answer_id($parts[1]);
			$answer->set_extra($value);
			$datamanager->create_user_answer($answer);
		}
	}
}
?>