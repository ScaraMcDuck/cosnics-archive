<?php
require_once dirname(__FILE__).'/assessment_results_viewer/results_viewer.class.php';
require_once dirname(__FILE__).'/assessment_results_table_admin/assessment_results_table_overview.class.php';
require_once dirname(__FILE__).'/assessment_results_table_admin/assessment_results_table_detail.class.php';
require_once dirname(__FILE__).'/assessment_results_table_student/assessment_results_table_overview.class.php';
require_once dirname(__FILE__).'/../../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__).'/assessment_tester.class.php';

class AssessmentToolResultsViewerComponent extends AssessmentToolComponent
{
	
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
		$crumbs[] = new BreadCrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS)), Translation :: get('ViewResults')); 
			
		$visible = $this->display_header($crumbs);
		if (!$visible)
		{
			return;
		}
		//general
		{
			$tree_id = Weblcms :: PARAM_CATEGORY;
			$params = array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS);
			$tree = new LearningObjectPublicationCategoryTree($this, $tree_id, $params);
			$this->set_parameter($tree_id, $_GET[$tree_id]);
			echo '<div style="width:18%; float: left; overflow: auto;">';
			echo $tree->as_html();
			echo '</div>';
			echo '<div style="width:80%; padding-left: 1%; float:right; ">';
		}
		if ($this->is_allowed(EDIT_RIGHT)) 
		{
			//echo Translation :: get('ViewAllResults').':';
			$table = new AssessmentResultsTableOverviewAdmin($this, $this->get_user());
		}
		else 
		{
			//echo Translation :: get('MyResults').':';
			$table = new AssessmentResultsTableOverviewStudent($this, $this->get_user());
		}
		echo $table->as_html();
		echo '</div>';
		$this->display_footer();
	}
	
	function view_assessment_results()
	{
		$pid = $_GET[AssessmentTool :: PARAM_ASSESSMENT];
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
		//echo Translation :: get('AssessmentResults');
		echo $assessment->get_title();
		echo '</div>';
		//TODO: wrong translation on assessment? 'Assessment process: 3'
		//echo Translation :: get('Assessment').': '.$assessment->get_title();
		//echo '<br/>'.Translation :: get('Type').': '.$assessment->get_assessment_type();
		//echo '<br/>'.Translation :: get('Description').': '.$assessment->get_description();
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
			$tot = $assessment->get_maximum_score();
			$pct = round($avg / $tot * 100, 2);
			$avg_line = $avg.'/'.$tot.' ('.$pct.'%)';
		}
		echo Translation :: get('AverageScore').': '.$avg_line;
		echo '<br/>'.Translation :: get('TimesTaken').': '.$track->get_times_taken($publication);
		echo '</div>';
		$table = new AssessmentResultsTableDetail($this, $this->get_user(), $_GET[AssessmentTool :: PARAM_ASSESSMENT]);
		echo $table->as_html();
		
		$this->display_footer();
	}
	
	function view_single_result() 
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$uaid = $_GET[AssessmentTool :: PARAM_USER_ASSESSMENT];
		
		$url = $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $uaid, AssessmentTool :: PARAM_ADD_FEEDBACK => '1'));
		$track = new WeblcmsAssessmentAttemptsTracker();
		$condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ID, $uaid);
		//$user_assessment = $datamanager->retrieve_user_assessment($uaid);
		$user_assessments = $track->retrieve_tracker_items($condition);
		$user_assessment = $user_assessments[0];
		$edit_rights = $this->is_allowed(EDIT_RIGHT);
		$subcomponent = ResultsViewer :: factory($user_assessment, $edit_rights, $url, $this);
		$subcomponent->build();
		
		if ($subcomponent->validate() && $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
		{
			$values = $subcomponent->exportValues();
			if (isset($values['submit']))
				$this->handle_validated_form($subcomponent, $datamanager, $user_assessment);
			else
			{
				$_SESSION['formvalues'] = $values;	
				$_SESSION['redirect_params'] = array(
					AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, 
					AssessmentTool :: PARAM_USER_ASSESSMENT => $uaid,
					AssessmentTool :: PARAM_ADD_FEEDBACK => 1
				);
				
				$this->redirect(null, null, false, array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_REPOVIEWER, AssessmentTool :: PARAM_REPO_TYPES => array('feedback')));
			}
		}
		else 
		{
			$crumbs[] = new BreadCrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS)), Translation :: get('ViewResults')); 
			$crumbs[] = new BreadCrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $uaid)), Translation :: get('ViewSingleResult')); 
			
			$visible = $this->display_header($crumbs);
			if (!$visible)
			{
				return;
			}
			
			$formvalues = $_SESSION['formvalues'];
			if ($formvalues != null)
			{
				$_SESSION['formvalues'] = null;
				foreach ($formvalues as $id => $value)
				{
					$parts = split('_', $id);
					if ($parts[0] == 'feedback')
					{
						//print_r($parts);
						$control_id = $parts[1];
						$objects = $_GET['object'];
						if (is_array($objects))
							$object = $objects[0];
						else
							$object = $objects;
							
						$formvalues['ex_'.$control_id] = $objects;
						$doc = RepositoryDataManager :: get_instance()->retrieve_learning_object($objects);
						$formvalues['ex'.$control_id.'_name'] = $doc->get_title();
					}
				}
				
				$subcomponent->setDefaults($formvalues);
			}
			
			echo '<br/>';
			echo $subcomponent->toHtml();
			
			$this->display_footer();
		}
	}
	
	function handle_validated_form($subcomponent, $datamanager, $user_assessment)
	{
		$results = $subcomponent->exportValues();

		foreach ($results as $key => $value)
		{
			if (substr($key, 0, 5) == 'score')
			{
				$question_id = substr($key, 5);
				$track = new WeblcmsQuestionAttemptsTracker();
				$condition_ass = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $user_assessment->get_id());
				$condition_q = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_QUESTION_ID, $question_id);
				$condition = new AndCondition(array($condition_ass, $condition_q));
				$user_answers = $track->retrieve_tracker_items($condition);
				foreach ($user_answers as $user_answer)
				{
					$user_answer->set_score($value);
					if ($user_answer->get_answer() == null)
					 	$user_answer->set_answer(' ');
					 	
					 $user_answer->update();
				}
			}
			else if (substr($key, 0, 3) == 'ex_')
			{
				$question_id = substr($key, 3);
				$track = new WeblcmsQuestionAttemptsTracker();
				$condition_ass = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $user_assessment->get_id());
				$condition_q = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_QUESTION_ID, $question_id);
				$condition = new AndCondition(array($condition_ass, $condition_q));
				$user_answers = $track->retrieve_tracker_items($condition);
				foreach ($user_answers as $user_answer)
				{
					if ($value != '') {
						$user_answer->set_feedback($value);
					}
					else
					{
						$user_answer->set_feedback(0);
					}
					if ($user_answer->get_answer() == null)
					 	$user_answer->set_answer(' ');
					 	
					$user_answer->update(); 	
				}
			}
		}
		$user_assessment->set_total_score(AssessmentToolTesterComponent :: calculate_score($user_assessment));
		$user_assessment->update();
		$params = array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment->get_id());
		$this->redirect(null, null, false, $params);
	}
	
	function display_header($breadcrumbs = array())
	{
		if (!isset($_GET[AssessmentTool :: PARAM_INVITATION_ID]))
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
		
		if(isset($_GET[AssessmentTool :: PARAM_USER_ASSESSMENT]) && $this->is_allowed(EDIT_RIGHT))
		{
			$uaid = $_GET[AssessmentTool :: PARAM_USER_ASSESSMENT];
			$feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK];
			$feedback = 1 - $feedback;
			
			$label = ($feedback == 1)?'AddFeedback':'HideFeedbackForm';
			$action_bar->add_tool_action(
				new ToolbarItem(
					Translation :: get($label), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $uaid, AssessmentTool :: PARAM_ADD_FEEDBACK => $feedback)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
				)
			);
		}
		
		if (isset($_GET[AssessmentTool :: PARAM_ASSESSMENT]) && $this->is_allowed(EDIT_RIGHT))
		{
			$aid = $_GET[AssessmentTool :: PARAM_ASSESSMENT];
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