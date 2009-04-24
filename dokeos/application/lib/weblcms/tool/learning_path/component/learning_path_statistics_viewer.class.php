<?php

require_once dirname(__FILE__).'/../../../trackers/weblcms_lp_attempt_tracker.class.php';
require_once dirname(__FILE__).'/../../../trackers/weblcms_lpi_attempt_tracker.class.php';
require_once dirname(__FILE__) . '/learning_path_viewer/learning_path_tree.class.php';

class LearningPathToolStatisticsViewerComponent extends LearningPathToolComponent
{
	function run()
	{
		$trail = new BreadCrumbTrail();
		
		$pid = Request :: get('pid');
		
		if(!$pid)
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NoObjectSelected'));
			$this->display_footer();
		}
		
		$dm = WeblcmsDataManager :: get_instance();
		$publication = $dm->retrieve_learning_object_publication($pid);
		$root_object = $publication->get_learning_object();
		
		$parameters = array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_STATISTICS, 
													    Tool :: PARAM_PUBLICATION_ID => $pid);
		$url = $this->get_url($parameters);
		
		$trail->add(new BreadCrumb($url, Translation :: get('Statistics') . ' ' .
													    Translation :: get('of') . ' ' . $root_object->get_title()));

		$attempt_id = Request :: get('attempt_id');
		
		if($attempt_id)
		{
			$tracker = $this->retrieve_tracker($attempt_id);
			$attempt_data = $this->retrieve_tracker_items($tracker);
			$menu = $this->get_menu($root_object->get_id(), null, $pid, $attempt_data);
			
			$parameters['attempt_id'] = $attempt_id; 
			$url = $this->get_url($parameters);			
			$trail->add(new BreadCrumb($url, Translation :: get('AttemptDetails')));
			
			$cid = Request :: get('cid');
			if($cid)
			{
				$parameters['cid'] = $cid; 
				$url = $this->get_url($parameters);			
				$trail->add(new BreadCrumb($url, Translation :: get('ItemDetails')));
			}
			
			require_once(Path :: get_application_path() . 'lib/weblcms/reporting/templates/learning_path_progress_reporting_template.class.php');
			$objects = $menu->get_objects();
			$template = new LearningPathProgressReportingTemplate($objects[$cid]);
			$template->set_reporting_blocks_function_parameters(array('objects' => $menu->get_objects(), 'attempt_data' => $attempt_data, 'cid' => $cid, 'url' => $url));
			$display = $template->to_html();
		}
		else 
		{
			require_once(Path :: get_application_path() . 'lib/weblcms/reporting/templates/learning_path_attempts_reporting_template.class.php');
			$template = new LearningPathAttemptsReportingTemplate();
			$template->set_reporting_blocks_function_parameters(array('publication' => $publication, 'course' => $this->get_course_id(), 'url' => $url));
			$display = $template->to_html();
		}
													    
		$this->display_header($trail);
		echo $display;
		$this->display_footer();
	}
	
	private function get_menu($root_object_id, $selected_object_id, $pid, $lpi_tracker_data)
	{
		$menu = new LearningPathTree($root_object_id, $selected_object_id, 
			'?go=courseviewer&course=' . $_GET['course'] . '&application=weblcms&tool=learning_path&tool_action=view&pid=' . 
			$pid . '&'.LearningPathTool :: PARAM_LP_STEP.'=%s', $lpi_tracker_data);
		
		return $menu;
	}
	
	private function retrieve_tracker($attempt_id)
	{
		$condition = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_ID, $attempt_id);
		$dummy = new WeblcmsLpAttemptTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);
		return $trackers[0];
	}
	
	private function retrieve_tracker_items($lp_tracker)
	{
		$lpi_attempt_data = array();
		
		$condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_LP_VIEW_ID, $lp_tracker->get_id());
		
		$dummy = new WeblcmsLpiAttemptTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);
		
		foreach($trackers as $tracker)
		{
			$item_id = $tracker->get_lp_item_id();
			if(!$lpi_attempt_data[$item_id])
			{
				$lpi_attempt_data[$item_id]['score'] = 0;
				$lpi_attempt_data[$item_id]['time'] = 0;
			}
			
			$lpi_attempt_data[$item_id]['trackers'][] = $tracker;
			$lpi_attempt_data[$item_id]['size']++;
			$lpi_attempt_data[$item_id]['score'] += $tracker->get_score();
			if($tracker->get_total_time())
				$lpi_attempt_data[$item_id]['time'] += $tracker->get_total_time();
			
			if($tracker->get_status() == 'completed')
				$lpi_attempt_data[$item_id]['completed'] = 1;
			else
				$lpi_attempt_data[$item_id]['active_tracker'] = $tracker;
		}
		//dump($lpi_attempt_data);
		return $lpi_attempt_data; 
		
	}
}
?>