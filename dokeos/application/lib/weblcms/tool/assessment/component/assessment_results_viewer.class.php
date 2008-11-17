<?php
require_once dirname(__FILE__).'/assessment_results_viewer/exercise_results_viewer.class.php';
require_once dirname(__FILE__).'/assessment_results_viewer/survey_results_viewer.class.php';
require_once dirname(__FILE__).'/assessment_results_viewer/assignment_results_viewer.class.php';
require_once dirname(__FILE__).'/assessment_results_table_admin/assessment_results_table_overview.class.php';
require_once dirname(__FILE__).'/assessment_results_table_admin/assessment_results_table_detail.class.php';
require_once dirname(__FILE__).'/assessment_results_table_student/assessment_results_table_overview.class.php';

class AssessmentToolResultsViewerComponent extends AssessmentToolComponent
{
	private $datamanager;
	
	function run() 
	{
		if (isset($_GET[AssessmentTool :: PARAM_USER_ASSESSMENT]))
		{
			$this->view_single_result();
		}
		else if (isset($_GET[AssessmentTool :: PARAM_ASSESSMENT]))
		{
			$this->view_assessment_results();
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
		
		if ($this->is_allowed(EDIT_RIGHT)) 
		{
			echo Translation :: get('View all course publications results').':';
			$table = new AssessmentResultsTableOverviewAdmin($this, $this->get_user());
		}
		else 
		{
			echo Translation :: get('My results').':';
			$table = new AssessmentResultsTableOverviewStudent($this, $this->get_user());
		}
		echo $table->as_html();
		
		$this->display_footer();
	}
	
	function view_assessment_results()
	{
		$visible = $this->display_header();
		if (!$visible || !$this->is_allowed(EDIT_RIGHT))
		{
			return;
		}
		
		$aid = $_GET[AssessmentTool :: PARAM_ASSESSMENT];
		$assessment = RepositoryDataManager :: get_instance()->retrieve_learning_object($aid);
		
		echo '<div class="learning_object">';
		echo '<div class="title">';
		echo Translation :: get('Assessment results');
		echo '</div>';
		//TODO: wrong translation on assessment? 'Assessment process: 3'
		echo Translation :: get('Assessment').': '.$assessment->get_title();
		echo '<br/>'.Translation :: get('Type').': '.$assessment->get_assessment_type();
		echo '<br/>'.Translation :: get('Description').': '.$assessment->get_description();
		echo '<div class="title">';
		echo Translation :: get('Statistics');
		echo '</div>';

		$avg = $assessment->get_average_score();
		$tot = $assessment->get_maximum_score();
		$pct = round($avg / $tot * 100, 2);
		echo Translation :: get('Average score').': '.$avg.'/'.$tot.' ('.$pct.'%)';
		echo '<br/>'.Translation :: get('Times taken').': '.$assessment->get_times_taken();
		echo '</div>';
		$table = new AssessmentResultsTableDetail($this, $this->get_user(), $_GET[AssessmentTool :: PARAM_ASSESSMENT]);
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

		$edit_rights = $this->is_allowed(EDIT_RIGHT);
		switch ($assessment->get_assessment_type()) 
		{
			case Assessment::TYPE_ASSIGNMENT:
				$subcomponent = new AssignmentResultsViewer($user_assessment, $edit_rights);
				break;
			case Assessment::TYPE_EXERCISE:
				$subcomponent = new ExerciseResultsViewer($user_assessment, $edit_rights);
				break;
			case Assessment::TYPE_SURVEY:
				$subcomponent = new SurveyResultsViewer($user_assessment, $edit_rights);
				break;
			default:
				$subcomponent = new ExerciseResultsViewer($user_assessment, $edit_rights);
				break;
		}
		echo '<br/>';
		$subcomponent->to_html();
		
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
		
		$this->action_bar = $this->get_toolbar();
		echo $this->action_bar->as_html();
		
		return true;
	}
}
?>