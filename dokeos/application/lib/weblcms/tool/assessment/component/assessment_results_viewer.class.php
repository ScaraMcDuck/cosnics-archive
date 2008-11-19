<?php
require_once dirname(__FILE__).'/assessment_results_viewer/exercise_results_viewer.class.php';
require_once dirname(__FILE__).'/assessment_results_viewer/survey_results_viewer.class.php';
require_once dirname(__FILE__).'/assessment_results_viewer/assignment_results_viewer.class.php';
require_once dirname(__FILE__).'/assessment_results_table_admin/assessment_results_table_overview.class.php';
require_once dirname(__FILE__).'/assessment_results_table_admin/assessment_results_table_detail.class.php';
require_once dirname(__FILE__).'/assessment_results_table_student/assessment_results_table_overview.class.php';
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
		$datamanager = WeblcmsDataManager :: get_instance();
		$uaid = $_GET[AssessmentTool :: PARAM_USER_ASSESSMENT];
		$url = $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $uaid, AssessmentTool :: PARAM_ADD_FEEDBACK => '1'));
		$user_assessment = $datamanager->retrieve_user_assessment($uaid);
		$repdm = RepositoryDataManager :: get_instance();
		$assessment = $repdm->retrieve_learning_object($user_assessment->get_assessment_id(), 'assessment');

		$edit_rights = $this->is_allowed(EDIT_RIGHT);
		switch ($assessment->get_assessment_type()) 
		{
			case Assessment::TYPE_ASSIGNMENT:
				$subcomponent = new AssignmentResultsViewer($user_assessment, $edit_rights, $url);
				break;
			case Assessment::TYPE_EXERCISE:
				$subcomponent = new ExerciseResultsViewer($user_assessment, $edit_rights, $url);
				break;
			case Assessment::TYPE_SURVEY:
				$subcomponent = new SurveyResultsViewer($user_assessment, $edit_rights, $url);
				break;
			default:
				$subcomponent = new ExerciseResultsViewer($user_assessment, $edit_rights, $url);
				break;
		}
		$subcomponent->build();
		
		if ($subcomponent->validate() && $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
		{
			echo 'wewts!';
			$results = $subcomponent->exportValues();
			print_r($results);
			foreach ($results as $key => $value)
			{
				if (substr($key, 0, 5) == 'score')
				{
					$user_question_id = substr($key, 5);
					//echo $user_question_id.' '.$value.'<br/>';
					$condition = new EqualityCondition(UserAnswer :: PROPERTY_USER_QUESTION_ID, $user_question_id);
					$user_answers = $datamanager->retrieve_user_answers($condition);
					$user_answer = $user_answers->next_result();
					$user_answer->set_score($value);
					$datamanager->update_user_answer($user_answer);
				}
				else if (substr($key, 0, 2) == 'ex')
				{
					$user_question_id = substr($key, 2);
					$user_question = $datamanager->retrieve_user_question($user_question_id);
					if ($value != 0) {
						$feedback_los = RepositoryDataManager :: get_instance()->retrieve_learning_objects('feedback');
						while ($feedback_lo = $feedback_los->next_result())
						{
							$feedback_objs[] = $feedback_lo;
						}
						$user_question->set_feedback($feedback_objs[$value-1]->get_id());
						//print_r($user_question);
						$datamanager->update_user_question($user_question);
					}
					else
					{
						$user_question->set_feedback('');
						//print_r($user_question);
						$datamanager->update_user_question($user_question);
					}
				}
			}
			//update user assessment total score
			$user_assessment->set_total_score(AssessmentToolTesterComponent :: calculate_score($user_assessment));
			WeblcmsDataManager :: get_instance()->update_user_assessment($user_assessment);
			//redirect
			$params = array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment->get_id());
			$this->redirect(null, null, false, $params);
		}
		else 
		{
			$visible = $this->display_header();
			if (!$visible)
			{
				return;
			}
			
			echo '<br/>';
			echo $subcomponent->toHtml();
			
			$this->display_footer();
		}
	}
	
	function display_header()
	{
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
	
	function get_toolbar()
	{
		$action_bar = parent :: get_toolbar();
		
		if(isset($_GET[AssessmentTool :: PARAM_USER_ASSESSMENT]) && $this->is_allowed(EDIT_RIGHT))
		{
			$uaid = $_GET[AssessmentTool :: PARAM_USER_ASSESSMENT];
			$feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK];
			$feedback = 1 - $feedback;
			
			$action_bar->add_tool_action(
				new ToolbarItem(
					Translation :: get('Add feedback and scores'), Theme :: get_common_img_path().'action_edit.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $uaid, AssessmentTool :: PARAM_ADD_FEEDBACK => $feedback)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
				)
			);
		}
		return $action_bar;
	}
}
?>