<?php
require_once dirname(__FILE__).'/assessment_results_viewer/results_viewer.class.php';
require_once dirname(__FILE__).'/assessment_results_table_admin/assessment_results_table_overview.class.php';
require_once dirname(__FILE__).'/assessment_results_table_admin/assessment_results_table_detail.class.php';
require_once dirname(__FILE__).'/assessment_results_table_student/assessment_results_table_overview.class.php';
require_once dirname(__FILE__).'/../../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__).'/assessment_tester.class.php';
require_once Path :: get_application_path().'lib/weblcms/trackers/weblcms_assessment_attempts_tracker.class.php';
require_once Path :: get_application_path().'lib/weblcms/trackers/weblcms_question_attempts_tracker.class.php';

class AssessmentToolResultsViewerComponent extends AssessmentToolComponent
{
	
	function run() 
	{
		if (Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT))
		{
			$this->view_single_result();
		}
		else if (Request :: get(AssessmentTool :: PARAM_ASSESSMENT))
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
		$crumbs[] = new BreadCrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS)), Translation :: get('ViewResults')); 
			
		$visible = $this->display_header($crumbs);
		if (!$visible)
		{
			return;
		}
		
		$tree_id = WeblcmsManager :: PARAM_CATEGORY;
		$params = array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS);
		$tree = new LearningObjectPublicationCategoryTree($this, $tree_id, $params);
		$this->set_parameter($tree_id, Request :: get($tree_id));
		echo '<div style="width:18%; float: left; overflow: auto;">';
		echo $tree->as_html();
		echo '</div>';
		echo '<div style="width:80%; padding-left: 1%; float:right; ">';
		
		if ($this->is_allowed(EDIT_RIGHT)) 
		{
			$table = new AssessmentResultsTableOverviewAdmin($this, $this->get_user());
		}
		else 
		{
			$table = new AssessmentResultsTableOverviewStudent($this, $this->get_user());
		}
		
		echo $table->as_html();
		echo '</div>';
		$this->display_footer();
	}
	
	function view_assessment_results()
	{
		$pid = Request :: get(AssessmentTool :: PARAM_ASSESSMENT);
		$crumbs[] = new BreadCrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS)), Translation :: get('ViewResults')); 
		$crumbs[] = new BreadCrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $pid)), Translation :: get('AssessmentResults'));	
		
		$visible = $this->display_header($crumbs);
		if (!$visible || !$this->is_allowed(EDIT_RIGHT))
		{
			return;
		}
		
		$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($pid);
		$assessment = $publication->get_learning_object();
		
		echo '<div class="learning_object" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/assessment.png);">';
		echo '<div class="title">';
		echo $assessment->get_title();
		echo '</div>';
		echo $assessment->get_description();
		echo '<div class="title">';
		echo Translation :: get('Statistics');
		echo '</div>';
		$track = new WeblcmsAssessmentAttemptsTracker();
		$avg = $track->get_average_score($publication);
		if (!isset($avg))
		{
			$avg_line = 'No results';
		}
		else
		{
			$avg_line = $avg . '%';
		}
		echo Translation :: get('AverageScore').': '.$avg_line;
		echo '<br/>'.Translation :: get('TimesTaken').': '.$track->get_times_taken($publication);
		echo '</div>';
		$table = new AssessmentResultsTableDetail($this, $this->get_user(), Request :: get(AssessmentTool :: PARAM_ASSESSMENT));
		echo $table->as_html();
		
		$this->display_footer();
	}
	
	private $user_assessment;
	
	function view_single_result()
	{
		$uaid = Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT);
		$track = new WeblcmsAssessmentAttemptsTracker();
		$condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ID, $uaid);
		$user_assessments = $track->retrieve_tracker_items($condition);
		$this->user_assessment = $user_assessments[0];
		
		$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($this->user_assessment->get_assessment_id());
		$object = $publication->get_learning_object();
		
		$_GET['display_action'] = 'view_result';

		$display = ComplexDisplay :: factory($this, $object->get_type());
      	$display->set_root_lo($object);		
      	
      	$this->display_header(new BreadcrumbTrail());
      	$display->run();
      	$this->display_footer();
	}
	
	//TODO: change following method to work with assessment module
	function retrieve_assessment_results()
	{
		$condition = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_LPI_ATTEMPT_ID, Request :: get('details'));

		$dummy = new WeblcmsLearningPathQuestionAttemptsTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);
		
		$results = array();
		
		foreach($trackers as $tracker)
		{
			$results[$tracker->get_question_cid()] = array(
				'answer' => $tracker->get_answer(),
				'feedback' => $tracker->get_feedback(),
				'score' => $tracker->get_score() 
			);
		}
		
		return $results;
	}
	
	//TODO: change following method to work with assessment module
	function change_answer_data($question_cid, $score, $feedback)
	{
		$conditions[] = new EqualityCondition(WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_LPI_ATTEMPT_ID, Request :: get('details'));
		$conditions[] = new EqualityCondition(WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $question_cid);
		$condition = new AndCondition($conditions);

		$dummy = new WeblcmsLearningPathQuestionAttemptsTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);
		$tracker = $trackers[0];
		$tracker->set_score($score);
		$tracker->set_feedback($feedback);
		$tracker->update();
	}
	
	//TODO: change following method to work with assessment module
	function change_total_score($total_score)
	{
		$condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_ID, Request :: get('details'));

		$dummy = new WeblcmsLpiAttemptTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);
		$lpi_tracker = $trackers[0];
		
		$lpi_tracker->set_score($total_score);
		$lpi_tracker->update();
	}
	
	function display_header($breadcrumbs = array())
	{
		if (!Request :: get(AssessmentTool :: PARAM_INVITATION_ID))
		{
			if (!$this->is_allowed(VIEW_RIGHT))
			{
				Display :: not_allowed();
				return false;
			}
		}
		$trail = new BreadcrumbTrail();
		foreach ($breadcrumbs as $breadcrumb)
		{
			$trail->add($breadcrumb);
		}
		parent :: display_header($trail);
		
		$this->action_bar = $this->get_toolbar();
		echo $this->action_bar->as_html();
		
		return true;
	}
	
	function get_toolbar()
	{
		$action_bar = parent :: get_toolbar();
		
		/*if(Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT) && $this->is_allowed(EDIT_RIGHT))
		{
			$uaid = Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT);
			$feedback = Request :: get(AssessmentTool :: PARAM_ADD_FEEDBACK);
			$feedback = 1 - $feedback;
			
			$label = ($feedback == 1)?'AddFeedback':'HideFeedbackForm';
			$action_bar->add_tool_action(
				new ToolbarItem(
					Translation :: get($label), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $uaid, AssessmentTool :: PARAM_ADD_FEEDBACK => $feedback)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
				)
			);
		}*/
		
		if (Request :: get(AssessmentTool :: PARAM_ASSESSMENT) && $this->is_allowed(EDIT_RIGHT))
		{
			$aid = Request :: get(AssessmentTool :: PARAM_ASSESSMENT);
			$action_bar->add_tool_action(new ToolbarItem(
				Translation :: get('DownloadDocuments'),
				Theme :: get_common_image_path().'action_save.png',
				$this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_SAVE_DOCUMENTS, AssessmentTool :: PARAM_ASSESSMENT => $aid)),
				ToolbarItem :: DISPLAY_ICON_AND_LABEL
			));
		}
		return $action_bar;
	}
}
?>