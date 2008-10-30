<?php
require_once dirname(__FILE__).'/assessment_results_viewer/exercise_results_viewer.class.php';
require_once dirname(__FILE__).'/assessment_results_viewer/survey_results_viewer.class.php';
require_once dirname(__FILE__).'/assessment_results_viewer/assignment_results_viewer.class.php';

class AssessmentToolResultsViewerComponent extends AssessmentToolComponent
{
	function run() 
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		
		$uaid = $_GET[AssessmentTool :: PARAM_USER_ASSESSMENT];
		$user_assessment = $datamanager->retrieve_user_assessment($uaid);
		$repdm = RepositoryDataManager :: get_instance();
		$assessment = $repdm->retrieve_learning_object($user_assessment->get_assessment_id(), 'assessment');
		
		if (!$this->is_allowed(VIEW_RIGHT))// || !$visible)
		{
			Display :: display_not_allowed();
			return;
		}
		
		$trail = new BreadcrumbTrail();
		
		
		$this->display_header($trail);
		
		echo '<br/>View results:';
		switch ($assessment->get_assessment_type()) 
		{
			case Assessment::TYPE_ASSIGNMENT:
				$subcomponent = new AssignmentResultsViewer($user_assessment);
				break;
			case Assessment::TYPE_EXERCISE:
				$subcomponent = new ExerciseResultsViewer($user_assessment);
				break;
			case Assessment::TYPE_SURVEY:
				$subcomponent = new SurveyResultsViewer($user_assessment);
				break;
			default:
				$subcomponent = new ExerciseResultsViewer($user_assessment);
				break;
		}
		echo '<br/>';
		echo $subcomponent->to_html();
		
		$this->display_footer();
	}
}
?>