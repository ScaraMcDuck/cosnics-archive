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
	
	function run() 
	{
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		
		$pid = $_GET['pid'];
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
		
		$step = $_GET[LearningPathTool :: PARAM_LP_STEP]?$_GET[LearningPathTool :: PARAM_LP_STEP]:1;
		$menu = $this->get_menu($root_object->get_id(), $step, $pid);
		
		$object = $menu->get_current_object();
		$cloi = $menu->get_current_cloi();
		$display = LearningPathLearningObjectDisplay :: factory($this, $object->get_type())->display_learning_object($object);
		
		$this->update_tracker($root_object, $cloi, $object);	
		
		$trail->merge($menu->get_breadcrumbs());
		
		$this->display_header($trail);
		echo '<br />';
		echo '<div style="width: 18%; overflow: auto; float: left;">';
		echo $menu->render_as_tree(). '<br /><br />';
		echo $this->get_progress_bar();
		echo $this->get_navigation_menu($menu->count_steps(), $step) . '<br /><br />';
		echo '</div>';
		echo '<div style="width: 80%; float: right; padding-left: 10px; min-height: 500px;">' . $display . '</div>';
		echo '<div class="clear">&nbsp;</div>';
		$this->display_footer();
	}
	
	private function get_menu($root_object_id, $selected_object_id, $pid)
	{
		$menu = new LearningPathTree($root_object_id, $selected_object_id, 
			$url_format = '?go=courseviewer&course=' . $_GET['course'] . '&application=weblcms&tool=learning_path&tool_action=view&pid=' . 
			$pid . '&'.LearningPathTool :: PARAM_LP_STEP.'=%s');
		
		return $menu;
	}
	
	private function get_navigation_menu($total_steps, $current_step)
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
				'label' => Translation :: get('NextNA'), 
				'img' => Theme :: get_common_image_path().'action_next_na.png'
			);
		}
		
		return DokeosUtilities :: build_toolbar($actions);
	}
	
	function get_publication_id()
	{
		return $this->pid;
	}
	
	private function get_progress_bar()
	{
		$html[] = '<div style="text-align: center; border: 1px solid black; height: 14px; width:100px;">';
		$html[] = '<div style="background-color: lightblue; height: 14px; width:10px; text-align: center;">';
		$html[] = '</div>';
		$html[] = '<div>10%</div>';
		$html[] = '</div><br />';
		
		return implode("\n", $html);
	}
	
	private function update_tracker($lp, $current_cloi, $current_object)
	{
		$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $lp->get_id());
		$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_USER_ID, $this->get_user_id());
		$conditions[] = new NotCondition(new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_PROGRESS, 100));
		$condition = new AndCondition($conditions);
		
		$dummy = new WeblcmsLpAttemptTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);
		$tracker = $trackers[0];
		
		if(!$tracker)
		{
			$tracker_id = Events :: trigger_event('attempt_learning_path', 'weblcms', array());
			dump($tracker_id);
		}
	}

}
?>