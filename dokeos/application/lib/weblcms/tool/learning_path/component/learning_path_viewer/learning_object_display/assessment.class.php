<?php

require_once dirname(__FILE__) . '/../learning_path_learning_object_display.class.php';

class AssessmentDisplay extends LearningPathLearningObjectDisplay
{
	function display_learning_object($assessment)
	{
		$link = $this->get_parent()->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_CLO, 'pid' => $assessment->get_id()));
		$html[] = $this->display_link($link);
		
		return implode("\n", $html);
	}
}

/*require_once dirname(__FILE__) . '/../learning_path_learning_object_display.class.php';
require_once Path :: get_application_path().'lib/weblcms/tool/assessment/component/assessment_tester_form/assessment_tester_display.class.php';
require_once Path :: get_application_path().'lib/weblcms/tool/assessment/component/assessment_tester_form/assessment_score_calculator.class.php';
require_once Path :: get_application_path().'lib/weblcms/tool/assessment/component/assessment_results_viewer/results_viewer.class.php';
require_once Path :: get_application_path().'lib/weblcms/trackers/weblcms_learning_path_assessment_attempts_tracker.class.php';
require_once Path :: get_repository_path().'lib/learning_object/assessment/assessment.class.php';
require_once Path :: get_repository_path().'lib/learning_object/survey/survey.class.php';

class AssessmentDisplay extends LearningPathLearningObjectDisplay
{
	private $datamanager;

	private $assessment;

	function display_learning_object($assessment)
	{
		$trackers = $this->get_parent()->get_trackers();
		$lpi_tracker = $trackers['lpi_tracker'];

		if($lpi_tracker->get_status() == 'completed')
		{
			return Display :: warning_message(Translation :: get('LearningPathItemIsCompleted'), true);
		}

		$this->assessment = $assessment;
		$this->datamanager = WeblcmsDataManager :: get_instance();

		$url = $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, Tool :: PARAM_PUBLICATION_ID => $_GET[Tool :: PARAM_PUBLICATION_ID], LearningPathTool :: PARAM_LP_STEP => $_GET[LearningPathTool :: PARAM_LP_STEP]));

		if (!$this->get_parent()->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		if (!isset($_SESSION['started']))
		{
			$_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE] = null;
			$_SESSION['formvalues'] = null;
			$_SESSION['started'] = true;

		}

		$this->create_tracker($assessment);

		if (isset($_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE]))
			$page = $_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE];
		else
			$page = 1;

		$form_display = new AssessmentTesterDisplay($this, $this->assessment);

		$html = array();

		$result = $form_display->build_form($url, $page);
		if($result == 'form')
		{
			$html[] = $form_display->as_html();
		}
		else
		{
			$html[] = $result;
		}

		$trackers = $this->get_parent()->get_trackers();
		$lpi_tracker = $trackers['lpi_tracker'];

		$html[] = '<script languages="JavaScript">';
		$html[] = '    var tracker_id = ' . $lpi_tracker->get_id() . ';';
		$html[] = '</script>';
		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_APP_PATH) . 'lib/weblcms/tool/learning_path/javascript/learning_path_item.js');

		return implode("\n", $html);
	}

	function create_tracker($assessment)
	{
		$trackers = $this->get_parent()->get_trackers();
		$lpi_tracker = $trackers['lpi_tracker'];

		$conditions[] = new EqualityCondition(WeblcmsLearningPathAssessmentAttemptsTracker :: PROPERTY_COURSE_ID, $this->get_parent()->get_course_id());
		$conditions[] = new EqualityCondition(WeblcmsLearningPathAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $lpi_tracker->get_id());
		$conditions[] = new EqualityCondition(WeblcmsLearningPathAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->get_parent()->get_user_id());
		$conditions[] = new EqualityCondition(WeblcmsLearningPathAssessmentAttemptsTracker :: PROPERTY_LEARNING_PATH_ID, $this->get_parent()->get_publication_id());
		$condition = new AndCondition($conditions);

		$tracker = new WeblcmsLearningPathAssessmentAttemptsTracker();
		$trackers = $tracker->retrieve_tracker_items($condition);
		$ass_tracker = $trackers[0];

		if(!$ass_tracker)
		{
			$args = array(
				'assessment_id' => $lpi_tracker->get_id(),
				'user_id' => $this->get_parent()->get_user_id(),
				'learning_path_id' => $this->get_parent()->get_publication_id(),
				'course_id' => $this->get_parent()->get_course_id(),
				'total_score' => 0
			);
			//dump($args);
			$tracker_id = $tracker->track($args);
		}
		else
		{
			$tracker_id = $ass_tracker->get_id();
			$lpi_tracker->set_start_time(time());
			$lpi_tracker->update();
		}
		//dump($tracker_id);
		$_SESSION['assessment_tracker'] = $tracker_id;
	}

	function redirect_to_score_calculator($values = null)
	{
		//dump($_SESSION);
		//unset($_SESSION['assessment_tracker']);
		//unset($_SESSION['started']);
		if ($values == null)
			$values = $_SESSION['formvalues'];

		$score_calculator = new AssessmentScoreCalculator();
		$score_calculator->build_answers($values, $this->assessment, $this->datamanager, $this, 'learning_path');
		$uaid = $_SESSION['assessment_tracker'];
		unset($_SESSION['assessment_tracker']);
		unset($_SESSION['started']);

		//WeblcmsDataManager :: get_instance()->create_user_assessment($user_assessment);
		$params = array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $uaid);
		if ($this->invitation != null)
		{
			$this->invitation->set_valid(false);
			$this->datamanager->update_survey_invitation($this->invitation);
			$params[AssessmentTool::PARAM_INVITATION_ID] = $this->invitation->get_invitation_code();
		}
		$track = new WeblcmsLearningPathAssessmentAttemptsTracker();
		$condition = new EqualityCondition(WeblcmsLearningPathAssessmentAttemptsTracker :: PROPERTY_ID, $uaid);
		$items = $track->retrieve_tracker_items($condition);
		$user_assessment = $items[0];
		$user_assessment->set_assessment_id($this->assessment);

		$results_form = ResultsViewer :: factory($user_assessment, false, null, 'WeblcmsLearningPathAssessmentAttemptsTracker');
		$results_form->build();

		$trackers = $this->get_parent()->get_trackers();
		$lpi_tracker = $trackers['lpi_tracker'];
		$lpi_tracker->set_status('completed');

		$score = $user_assessment->get_total_score();
		$max_total_score = $this->assessment->get_maximum_score();
		$pct_score = round(($score / $max_total_score) * 100, 2);
		$lpi_tracker->set_score($pct_score);
		$lpi_tracker->set_total_time($lpi_tracker->get_total_time() + (time() - $lpi_tracker->get_start_time()));
		$lpi_tracker->update();

		//dump($uaid);

		//dump($user_assessment);
		return $results_form->toHtml();
	}

	function redirect_to_repoviewer()
	{
		$_SESSION['redirect_params'] = array(
			WeblcmsManager :: PARAM_TOOL => 'learning_path',
			LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH,
			Tool :: PARAM_PUBLICATION_ID => $_GET[Tool :: PARAM_PUBLICATION_ID],
			LearningPathTool :: PARAM_LP_STEP => $_GET[LearningPathTool :: PARAM_LP_STEP]
		);
		$this->redirect(null, false, array(WeblcmsManager :: PARAM_TOOL => 'assessment', AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_REPOVIEWER, AssessmentTool :: PARAM_REPO_TYPES => array('document')));
	}

	function get_url($params)
	{
		return $this->get_parent()->get_url($params);
	}

	function redirect($message = '', $error_message = false, $parameters = array (), $filter = array(), $encode_entities = false, $type = Redirect :: TYPE_URL)
	{
		return $this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities, $type);
	}
	
	
}*/
?>