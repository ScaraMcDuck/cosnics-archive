<?php
/**
 * @package application.weblcms.tool.assessment.component
 */


require_once Path :: get_repository_path().'lib/learning_object/survey/survey.class.php';
require_once dirname(__FILE__).'/../survey_invitation.class.php';
require_once Path :: get_application_path().'lib/weblcms/trackers/weblcms_assessment_attempts_tracker.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';

class AssessmentToolTesterComponent extends AssessmentToolComponent
{
	private $datamanager;

	private $pub;
	private $invitation;
	private $assessment;
	private $iid;
	private $pid;
	private $active_tracker;

	function run()
	{
		// Retrieving assessment
		$this->datamanager = WeblcmsDataManager :: get_instance();
		if (Request :: get(Tool :: PARAM_PUBLICATION_ID))
		{
			$this->pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
			$this->pub = $this->datamanager->retrieve_learning_object_publication($this->pid);
			$this->assessment = $this->pub->get_learning_object();
			$this->set_parameter('pid', $this->pid);
		}
		
		if (Request :: get(AssessmentTool :: PARAM_INVITATION_ID))
		{
			$this->iid = Request :: get(AssessmentTool :: PARAM_INVITATION_ID);
			$condition = new EqualityCondition(SurveyInvitation :: PROPERTY_INVITATION_CODE, $this->iid);
			$this->invitation = $this->datamanager->retrieve_survey_invitations($condition)->next_result();
			$this->pub = $this->datamanager->retrieve_learning_object_publication($this->invitation->get_survey_id());
			$this->pid = $this->pub->get_id();
			$this->assessment = $this->pub->get_learning_object(); 
			$this->set_parameter(AssessmentTool :: PARAM_INVITATION_ID, $this->iid);
		}

		// Checking statistics
		
		$track = new WeblcmsAssessmentAttemptsTracker();
		$conditions[] = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $this->pid);
		$conditions[] = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->get_user_id());
		$condition = new AndCondition($conditions);
		$trackers = $track->retrieve_tracker_items($condition);
	
		$count = count($trackers);
		
		foreach($trackers as $tracker)
		{
			if($tracker->get_status() == 'not attempted')
			{
				$this->active_tracker = $tracker;
				$count--;
				break;
			}
		}
		
		if ($this->assessment->get_maximum_attempts() != 0 && $count >= $this->assessment->get_maximum_attempts())
		{
			Display :: not_allowed();
			return;
		}
		
		if(!$this->active_tracker)
		{
			$this->active_tracker = $this->create_tracker();
		}

		// Executing assessment
		
		if($this->assessment->get_assessment_type() == 'hotpotatoes')
		{
			$this->display_header(new BreadcrumbTrail());

			$path = $this->add_javascript();
			//$path = $this->assessment->get_test_path();
			echo '<iframe src="' . $path . '" width="100%" height="600">
  				 <p>Your browser does not support iframes.</p>
				 </iframe>';
			//require_once $path;
			$this->display_footer();
			exit();
		}
		else 
		{
			$display = ComplexDisplay :: factory($this, $this->assessment->get_type());
	        $display->set_root_lo($this->assessment);
	        	
			$this->display_header(new BreadcrumbTrail());
			$display->run();
			$this->display_footer();
		}

	}

	function create_tracker()
	{
		$args = array(
			'assessment_id' => $this->pid,
			'user_id' => $this->get_user_id(),
			'course_id' => $this->get_course_id(),
			'total_score' => 0
		);

		$tracker = Events :: trigger_event('attempt_assessment', 'weblcms', $args);

		return $tracker[0];
	}

	function get_user_id()
	{
		if ($this->assessment->get_assessment_type() == Survey :: TYPE_SURVEY)
		{
			if ($this->assessment->get_anonymous() == true)
				return 0;
		}
		return parent :: get_user_id();
	}
	
	function save_answer($complex_question_id, $answer, $score)
	{ 
		$parameters = array();
		$parameters['assessment_attempt_id'] = $this->active_tracker->get_id();
		$parameters['question_cid'] = $complex_question_id;
		$parameters['answer'] = $answer;
		$parameters['score'] = $score;
		$parameters['feedback'] = '';

		Events :: trigger_event('attempt_question', 'weblcms', $parameters); 
	}
	
	function finish_assessment($total_score)
	{
		$tracker = $this->active_tracker;
		
		$tracker->set_total_score($total_score);
		$tracker->set_total_time($tracker->get_total_time() + (time() - $tracker->get_start_time()));
		$tracker->set_status('completed');
		$tracker->update();
	}
	
	function get_current_attempt_id()
	{
		return $this->active_tracker->get_id();
	}
	
	function get_go_back_url()
	{
		return $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW));
	}

	//add_javascript

	function add_javascript()
	{
		$content = $this->read_file_content();
		$js_content = $this->replace_javascript($content);
		$path = $this->write_file_content($js_content);
		
		return $path;
	}
	
	private function read_file_content()
	{
		$full_file_path = Path :: get(SYS_REPO_PATH) . $this->assessment->get_path();
		
		if(is_file($full_file_path)) 
		{
			if (!($fp = fopen(urldecode($full_file_path), "r"))) 
			{
				return "";
			}
			$contents = fread($fp, filesize($full_file_path));
			fclose($fp);
			return $contents;
	  	}
	}
	
	private function write_file_content($content)
	{
		$full_file_path = Path :: get(SYS_REPO_PATH) . substr($this->assessment->get_path(), 0, strlen($this->assessment->get_path()) - 4) . '.' . Session :: get_user_id() . '.t.htm';
		$full_web_path = Path :: get(WEB_REPO_PATH) . substr($this->assessment->get_path(), 0, strlen($this->assessment->get_path()) - 4) . '.' . Session :: get_user_id() . '.t.htm';
		Filesystem::remove($full_file_path);
		
		if (($fp = fopen(urldecode($full_file_path), "w"))) 
		{
			fwrite($fp,$content);
			fclose($fp);
		}
		
		return $full_web_path;
	}
	
	private function replace_javascript($content)
	{
		$mit = "function Finish(){";
		$js_content = "var SaveScoreVariable = 0; // This variable included by Dokeos System\n".
					"function mySaveScore() // This function included by Dokeos System\n".
					"{\n".
					"   if (SaveScoreVariable==0)\n".
					"		{\n".
					"			SaveScoreVariable = 1;\n".
				//	"			var result=jQuery.ajax({type: 'POST', url:'".Path ::get(WEB_PATH)."application/lib/weblcms/ajax/hotpotatoes_save_score.php', data: {id: " . $this->active_tracker->get_id() . ", score: Score}, async: false}).responseText;\n" .
					"			alert(result);" .
					"			if (C.ie)\n".
					"			{\n".
				//	"				window.alert(Score);\n".
					"				document.parent.location.href=\"" . $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_ASSESSMENTS)) . "\"\n".
					"			}\n".
					"			else\n".
					"			{\n".
				//	"				window.alert(Score);\n".
					"				window.parent.location.href=\"" . $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_ASSESSMENTS)) . "\"\n".
					"			}\n".
					"		}\n".
					"}\n".
					"// Must be included \n".
					"function Finish(){\n".
					" mySaveScore();";
		$newcontent = str_replace($mit,$js_content,$content);
		$prehref="<!-- BeginTopNavButtons -->";
		$posthref="<!-- BeginTopNavButtons --><!-- edited by Dokeos -->";
		$newcontent = str_replace($prehref,$posthref,$newcontent);
		
		$jquery_content = "<head>\n<script src='" . Path :: get(WEB_PATH) . "plugin/jquery/jquery.min.js' type='text/javascript'></script>";
		$add_to = '<head>';
		$newcontent = str_replace($add_to,$jquery_content,$newcontent);
		
		return $newcontent;
	}
}
?>