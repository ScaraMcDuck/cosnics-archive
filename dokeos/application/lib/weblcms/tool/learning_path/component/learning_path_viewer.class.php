<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
//require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
//require_once Path :: get_repository_path() . 'lib/complex_learning_object_menu.class.php';
require_once dirname(__FILE__) . '/learning_path_viewer/learning_path_tree.class.php';
require_once dirname(__FILE__) . '/learning_path_viewer/learning_path_learning_object_display.class.php';
require_once dirname(__FILE__).'/../../../trackers/weblcms_lp_attempt_tracker.class.php';
require_once dirname(__FILE__).'/../../../trackers/weblcms_lpi_attempt_tracker.class.php';

class LearningPathToolViewerComponent extends LearningPathToolComponent
{
	private $pid;
	private $trackers;
	private $lpi_attempt_data;
	
	function run() 
	{
		// Check for rights
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$trail = new BreadcrumbTrail();
		
		// Check and retrieve publication
		$pid = Request :: get('pid');
		$this->pid = $pid;
		
		if(!$pid)
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NoObjectSelected'));
			$this->display_footer();
		}
		
		$dm = WeblcmsDataManager :: get_instance();
		$publication = $dm->retrieve_learning_object_publication($pid);
		$root_object = $publication->get_learning_object();
		
		// Do tracking stuff
		$this->trackers['lp_tracker'] = $this->retrieve_lp_tracker($root_object);	
		$lpi_attempt_data = $this->retrieve_tracker_items($this->trackers['lp_tracker']);
		
		// Retrieve tree menu
		if(Request :: get('lp_action') == 'view_progress')
		{
			$step = null;
		}
		else
		{
			$step = $_GET[LearningPathTool :: PARAM_LP_STEP]?$_GET[LearningPathTool :: PARAM_LP_STEP]:1;
		}
		
		$menu = $this->get_menu($root_object->get_id(), $step, $pid, $lpi_attempt_data);
		$object = $menu->get_current_object();
		$cloi = $menu->get_current_cloi();
		
		// Update main tracker
		$this->trackers['lp_tracker']->set_progress($menu->get_progress());
		$this->trackers['lp_tracker']->update();
		
		// Retrieve correct display and show it on screen
		if(Request :: get('lp_action') == 'view_progress')
		{
			$url = $this->get_url(array('tool_action' => 'view', 'pid' => $pid, 'lp_action' => 'view_progress'));
			require_once(Path :: get_application_path() . 'lib/weblcms/reporting/templates/learning_path_progress_reporting_template.class.php');
			$objects = $menu->get_objects();
			$cid = Request :: get('cid');
			$template = new LearningPathProgressReportingTemplate($objects[$cid]);
			$template->set_reporting_blocks_function_parameters(array('objects' => $objects, 'attempt_data' => $lpi_attempt_data, 'cid' => $cid, 'url' => $url));
			$display = $template->to_html();
		}
		else
		{	
			$lpi_tracker = $menu->get_current_tracker();
			if(!$lpi_tracker)
			{
				$lpi_tracker = $this->create_lpi_tracker($this->trackers['lp_tracker'], $cloi);
			}
			else
			{
				$lpi_tracker->set_start_time(time());
				$lpi_tracker->update();
			}
			
			$this->trackers['lpi_tracker'] = $lpi_tracker;
			
			$display = LearningPathLearningObjectDisplay :: factory($this, $object->get_type())->display_learning_object($object);
		}
		
		$trail->merge($menu->get_breadcrumbs());
		
		$this->display_header($trail);
		echo '<br />';
		echo '<div style="width: 18%; overflow: auto; float: left;">';
		echo $menu->render_as_tree(). '<br /><br />';
		echo $this->get_progress_bar($menu->get_progress());
		echo $this->get_navigation_menu($menu->count_steps(), $step) . '<br /><br />';
		echo '</div>';
		echo '<div style="width: 80%; float: right; padding-left: 10px; min-height: 500px;">' . $display . '</div>';
		echo '<div class="clear">&nbsp;</div>';
		$this->display_footer();
	}
	
	/**
	 * Creates the tree menu for the learning path
	 *
	 * @param int $root_object_id
	 * @param int $selected_object_id
	 * @param int $pid
	 * @param LearningPathAttemptTracker $lp_tracker
	 * @return HTML code of the menu
	 */
	private function get_menu($root_object_id, $selected_object_id, $pid, $lp_tracker)
	{
		$menu = new LearningPathTree($root_object_id, $selected_object_id, 
			'?go=courseviewer&course=' . $_GET['course'] . '&application=weblcms&tool=learning_path&tool_action=view&pid=' . 
			$pid . '&'.LearningPathTool :: PARAM_LP_STEP.'=%s', $lp_tracker);
		
		return $menu;
	}
	
	// Getters & Setters
	
	function get_publication_id()
	{
		return $this->pid;
	}
	
	function get_trackers()
	{
		return $this->trackers;
	}
	
	// Layout functionality
	
	/**
	 * Retrieves the navigation menu for the learning path
	 *
	 * @param int $total_steps
	 * @param int $current_step
	 * @return HTML of the navigation menu
	 */
	private function get_navigation_menu($total_steps, $current_step)
	{
		if(!$current_step)
		{
			$actions[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, LearningPathTool :: PARAM_PUBLICATION_ID => $_GET['pid'], 'step' => $total_steps)), 
				'label' => Translation :: get('Previous'), 
				'img' => Theme :: get_common_image_path().'action_prev.png'
			);
			
			$actions[] = array( 
				'label' => Translation :: get('NextNA'), 
				'img' => Theme :: get_common_image_path().'action_next_na.png'
			);
		}
		else 
		{
		
			if($current_step > 1)
			{
				$actions[] = array(
					'href' => $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, LearningPathTool :: PARAM_PUBLICATION_ID => $_GET['pid'], 'step' => $current_step - 1)), 
					'label' => Translation :: get('Previous'), 
					'img' => Theme :: get_common_image_path().'action_prev.png'
				);
			}
			else
			{
				$actions[] = array( 
					'label' => Translation :: get('PreviousNA'), 
					'img' => Theme :: get_common_image_path().'action_prev_na.png'
				);
			}
			
			if($current_step < $total_steps)
			{	
				$actions[] = array(
					'href' => $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, LearningPathTool :: PARAM_PUBLICATION_ID => $_GET['pid'], 'step' => $current_step + 1)), 
					'label' => Translation :: get('Next'), 
					'img' => Theme :: get_common_image_path().'action_next.png'
				);
			}
			else
			{
				$actions[] = array(
					'href' => $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, LearningPathTool :: PARAM_PUBLICATION_ID => $_GET['pid'], 'lp_action' => 'view_progress')), 
					'label' => Translation :: get('Next'), 
					'img' => Theme :: get_common_image_path().'action_next.png'
				);
			}
		}
		
		return DokeosUtilities :: build_toolbar($actions);
	}
	
	/**
	 * Retrieves the progress bar for the learning path
	 *
	 * @param int $progress - The current progress
	 * @return HTML code of the progress bar
	 */
	private function get_progress_bar($progress)
	{
		$html[] = '<div style="position: relative; text-align: center; border: 1px solid black; height: 14px; width:100px;">';
		$html[] = '<div style="background-color: lightblue; height: 14px; width:' . $progress . 'px; text-align: center;">';
		$html[] = '</div>';
		$html[] = '<div style="width: 100px; text-align: center; position: absolute; top: 0px;">' . round($progress) . '%</div></div>';
		
		return implode("\n", $html);
	}
	
	// Tracker functionality
	
	/**
	 * Retrieves the learning path tracker for the current user
	 * @param LearningPath $lp
	 * @return A LearningPathAttemptTracker
	 */
	private function retrieve_lp_tracker($lp)
	{
		$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $lp->get_id());
		$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_USER_ID, $this->get_user_id());
		//$conditions[] = new NotCondition(new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_PROGRESS, 100));
		$condition = new AndCondition($conditions);
		
		$dummy = new WeblcmsLpAttemptTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);
		$lp_tracker = $trackers[0];
		
		if(!$lp_tracker)
		{
			$return = Events :: trigger_event('attempt_learning_path', 'weblcms', array('user_id' => $this->get_user_id(), 'course_id' => $this->get_course_id(), 'lp_id' => $lp->get_id()));
			$lp_tracker = $return[0];
		}
		
		return $lp_tracker;
	}
	
	/**
	 * Retrieve the tracker items for the current LearningPathAttemptTracker
	 *
	 * @param LearningPathAttemptTracker $lp_tracker
	 * @return array of LearningPathItemAttemptTracker
	 */
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
	
	/**
	 * Creates a learning path item tracker
	 *
	 * @param LearningPathAttemptTracker $lp_tracker
	 * @param ComplexLearningObjectItem $current_cloi
	 * @return array LearningPathItemAttemptTracker
	 */
	private function create_lpi_tracker($lp_tracker, $current_cloi)
	{
		$return = Events :: trigger_event('attempt_learning_path_item', 'weblcms', array('lp_view_id' => $lp_tracker->get_id(), 'lp_item_id' => $current_cloi->get_id(), 'start_time' => time(), 'status' => 'incomplete'));
		$lpi_tracker = $return[0];
		
		return $lpi_tracker;
	}
	
	

}
?>