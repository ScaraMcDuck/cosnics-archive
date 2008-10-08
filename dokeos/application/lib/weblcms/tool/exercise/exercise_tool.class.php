<?php
/**
 * $Id: announcementtool.class.php 9200 2006-09-04 13:40:47Z bmol $
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../repository_tool.class.php';
require_once dirname(__FILE__).'/exercise_tool_component.class.php';
/**
 * This tool allows a user to publish exercises in his or her course.
 */
class ExerciseTool extends RepositoryTool
{
	const ACTION_VIEW_EXERCISES = 'view';
	/*
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		
		switch($action) 
		{
			case self :: ACTION_PUBLISH:
				$component = ExerciseToolComponent :: factory('Publisher', $this);
				break;
			case self :: ACTION_VIEW_EXERCISES:
				$component = ExerciseToolComponent :: factory('Viewer', $this);
				break;
			default:
				$component = ExerciseToolComponent :: factory('Viewer', $this);
				break;
		}
		
		$component->run();
		/*$trail = new BreadcrumbTrail();
		
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		if (isset($_GET['admin']))
		{
			$_SESSION['exerciseadmin'] = $_GET['admin'];
		}
		if ($_SESSION['exerciseadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			require_once dirname(__FILE__).'/../../learning_object_publisher.class.php';
			$pub = new LearningObjectPublisher($this, 'exercise', true);
			$html[] = '<p><a href="' . $this->get_url(array('admin' => 0), true) . '"><img src="'.Theme :: get_common_img_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
			$this->display_header($trail);
			echo implode("\n",$html);
			$this->display_footer();
		}
		else
		{
			$this->display_header($trail);
			if($this->is_allowed(ADD_RIGHT))
			{
				echo '<p><a href="' . $this->get_url(array('admin' => 1), true) . '"><img src="'.Theme :: get_common_img_path().'action_publish.png" alt="'.Translation :: get('Publish').'" style="vertical-align:middle;"/> '.Translation :: get('Publish').'</a></p>';
			}
			echo $this->perform_requested_actions();
			$browser = new ExerciseBrowser($this);
			echo $browser->as_html();
			$this->display_footer();
		}*/
	}
}
?>