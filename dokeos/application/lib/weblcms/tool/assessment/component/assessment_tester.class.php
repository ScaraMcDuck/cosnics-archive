<?php
/**
 * @package application.weblcms.tool.exercise.component
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
		
		$this->display_header($trail);
		
		//echo "Take test: <br/>".$assessment->get_title()."<br/>";
		//echo "It's not a bug, it's a feature!";
		$tester_form = new AssessmentTesterForm($assessment);
		echo $tester_form->toHtml();
		
		$this->display_footer();
	}
}
?>