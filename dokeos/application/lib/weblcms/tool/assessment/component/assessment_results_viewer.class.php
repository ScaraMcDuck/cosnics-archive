<?php
require_once dirname(__FILE__).'/assessment_results_viewer/exercise_results_viewer.class.php';
require_once dirname(__FILE__).'/assessment_results_viewer/survey_results_viewer.class.php';
require_once dirname(__FILE__).'/assessment_results_viewer/assignment_results_viewer.class.php';
require_once dirname(__FILE__).'/assessment_results_table/assessment_results_table.class.php';

class AssessmentToolResultsViewerComponent extends AssessmentToolComponent
{
	private $datamanager;
	
	function run() 
	{
		if (isset($_GET[AssessmentTool :: PARAM_USER_ASSESSMENT]))
		{
			$this->view_single_result();
		}
		else if (isset($_GET[Tool :: PARAM_PUBLICATION_ID]))
		{
			$this->view_publication_results();
		}
		else 
		{
			$this->view_all_results();	
		}
		
	}
	
	function view_all_results()
	{
		$visible = $this->display_header();
		if (!$visible)
		{
			return;
		}
		
		echo 'View all course publications results:';
		$table = new AssessmentResultsTable($this, $this->get_user());
		echo $table->as_html();
		
		$this->display_footer();
	}
	
	function view_publication_results()
	{
		$visible = $this->display_header();
		if (!$visible)
		{
			return;
		}
		
		$pid = $_GET[Tool :: PARAM_PUBLICATION_ID];
		$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($pid);
		
		echo 'View publication results: '.$publication->get_learning_object()->get_title();
		$table = new AssessmentResultsTable($this, $this->get_user(), $_GET[Tool :: PARAM_PUBLICATION_ID]);
		echo $table->as_html();
		
		$this->display_footer();
	}
	
	function view_single_result() 
	{
		$visible = $this->display_header();
		if (!$visible)
		{
			return;
		}
		
		$uaid = $_GET[AssessmentTool :: PARAM_USER_ASSESSMENT];
		$user_assessment = $this->datamanager->retrieve_user_assessment($uaid);
		$repdm = RepositoryDataManager :: get_instance();
		$assessment = $repdm->retrieve_learning_object($user_assessment->get_assessment_id(), 'assessment');

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
		echo implode('<br/>', $subcomponent->to_html());
		
		$this->display_footer();
	}
	
	function display_header()
	{
		$this->datamanager = WeblcmsDataManager :: get_instance();
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return false;
		}
		
		$trail = new BreadcrumbTrail();
		parent :: display_header($trail);
		
		return true;
	}
}
?>