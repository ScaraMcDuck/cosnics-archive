<?php
/**
 * @package application.weblcms.tool.assessment.component
 */

require_once Path :: get_application_path().'lib/assessment/trackers/assessment_assessment_attempts_tracker.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';

class AssessmentManagerViewerComponent extends AssessmentManagerComponent
{
	private $datamanager;

	private $pub;
	private $assessment;
	private $pid;
	private $active_tracker;

	function run()
	{
		// Retrieving assessment
		$this->datamanager = AssessmentDataManager :: get_instance();
		if (Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION))
		{
			$this->pid = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);
			$this->pub = $this->datamanager->retrieve_assessment_publication($this->pid);
			$assessment_id = $this->pub->get_learning_object();
			$this->assessment = RepositoryDataManager :: get_instance()->retrieve_learning_object($assessment_id);
			$this->set_parameter(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION, $this->pid);
		}
		
		if (Request :: get(AssessmentManager :: PARAM_INVITATION_ID))
		{
			$condition = new EqualityCondition(SurveyInvitation :: PROPERTY_INVITATION_CODE, Request :: get(AssessmentManager :: PARAM_INVITATION_ID));
			$invitation = $this->datamanager->retrieve_survey_invitations($condition)->next_result();
			
			$this->pid = $invitation->get_survey_id();
			$this->pub = $this->datamanager->retrieve_assessment_publication($this->pid);
			$assessment_id = $this->pub->get_learning_object();
			$this->assessment = RepositoryDataManager :: get_instance()->retrieve_learning_object($assessment_id);
			$this->set_parameter(AssessmentManager :: PARAM_INVITATION_ID, Request :: get(AssessmentManager :: PARAM_INVITATION_ID));
		}
		
		if ($this->pub && !$this->pub->is_visible_for_target_user($this->get_user()))
		{
			$this->not_allowed($trail, false);
		}
		
		// Checking statistics
		
		$track = new AssessmentAssessmentAttemptsTracker();
		$conditions[] = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $this->pid);
		$conditions[] = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->get_user_id());
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

        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $this->pid)), Translation :: get('TakeAssessment')));
		
		// Executing assessment
		
		if($this->assessment->get_assessment_type() == 'hotpotatoes')
		{
			$this->display_header($trail);

			$path = $this->add_javascript();
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

			$this->display_header($trail);
			$display->run();
			$this->display_footer();
		}

	}
	
	function create_tracker()
	{
		$args = array(
			'assessment_id' => $this->pid,
			'user_id' => $this->get_user_id(),
			'total_score' => 0
		);

		$tracker = Events :: trigger_event('attempt_assessment', 'assessment', $args);

		return $tracker[0];
	}

	function get_user_id()
	{
		if ($this->assessment->get_assessment_type() == Survey :: TYPE_SURVEY)
		{
			//if ($this->assessment->get_anonymous() == true)
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

		Events :: trigger_event('attempt_question', 'assessment', $parameters); 
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
		return $this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS, 
							  	    AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => null));
	}
	
	//Add javascript
	
	function add_javascript($course)
	{
		$content = $this->read_file_content();
		$js_content = $this->replace_javascript($content, $course);
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
					"			var result=jQuery.ajax({type: 'POST', url:'".Path ::get(WEB_PATH)."application/lib/assessment/ajax/hotpotatoes_save_score.php', data: {id: " . $this->active_tracker->get_id() . ", score: Score}, async: false}).responseText;\n" .
				//	"			alert(result);" .
					"			if (C.ie)\n".
					"			{\n".
				//	"				window.alert(Score);\n".
					"				document.parent.location.href=\"" . $this->get_browse_assessment_publications_url() . "\"\n".
					"			}\n".
					"			else\n".
					"			{\n".
				//	"				window.alert(Score);\n".
					"				window.parent.location.href=\"" . $this->get_browse_assessment_publications_url() . "\"\n".
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