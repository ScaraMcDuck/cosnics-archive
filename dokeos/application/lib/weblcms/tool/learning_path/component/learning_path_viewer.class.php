<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
//require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
//require_once Path :: get_repository_path() . 'lib/complex_learning_object_menu.class.php';
require_once dirname(__FILE__) . '/learning_path_viewer/learning_path_tree.class.php';
require_once dirname(__FILE__) . '/learning_path_viewer/learning_path_learning_object_display.class.php';

class LearningPathToolViewerComponent extends LearningPathToolComponent
{
	function run() 
	{
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		
		$pid = $_GET['pid'];
		if(!$pid)
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NoObjectSelected'));
			$this->display_footer();
		}
		
		$dm = WeblcmsDataManager :: get_instance();
		$publication = $dm->retrieve_learning_object_publication($pid);
		$root_object = $publication->get_learning_object();	
		
		$step = $_GET['step']?$_GET['step']:1;
		$menu = $this->get_menu($root_object->get_id(), $step, $pid);
		
		$object = $menu->get_object($step);
		$display = LearningPathLearningObjectDisplay :: factory($object->get_type())->display_learning_object($object);
		
		$this->display_header($trail);
		echo '<br />';
		echo '<div style="width: 18%; overflow: auto; float: left;">' . $menu->render_as_tree() . '<br /></div>';
		echo '<div style="width: 80%; border-left: 1px solid black; float: right; padding-left: 10px; min-height: 500px;">' . $display . '</div>';
		$this->display_footer();
	}
	
	private function get_menu($root_object_id, $selected_object_id, $pid)
	{
		$menu = new LearningPathTree($root_object_id, $selected_object_id, 
			$url_format = '?go=courseviewer&course=' . $_GET['course'] . '&application=weblcms&tool=learning_path&tool_action=view&pid=' . 
			$pid . '&step=%s');
		
		return $menu;
	}

}
?>