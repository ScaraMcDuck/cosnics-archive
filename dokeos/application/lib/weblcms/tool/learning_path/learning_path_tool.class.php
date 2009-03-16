<?php
/**
 * $Id$
 * Learning path tool
 * @package application.weblcms.tool
 * @subpackage learning_path
 */
//require_once dirname(__FILE__).'/../repository_tool.class.php';
//require_once dirname(__FILE__).'/learning_path_browser.class.php';
require_once dirname(__FILE__).'/learning_path_tool_component.class.php';
/**
 * This tool allows a user to publish learning paths in his or her course.
 */
class LearningPathTool extends Tool
{
	const ACTION_VIEW_LEARNING_PATH = 'view';
	const ACTION_BROWSE_LEARNING_PATHS = 'browse';
	const ACTION_EXPORT_SCORM = 'exp_scorm';
	
	const PARAM_LEARNING_PATH = 'lp';
	const PARAM_LP_STEP = 'step';
	const PARAM_LEARNING_PATH_ID = 'lpid';
	
	// Inherited.
	function run()
	{
		$action = $this->get_action();
		$component = parent :: run();
		
		if ($component) return;
		
		switch($action)
		{
			case self :: ACTION_PUBLISH:
				$component = LearningPathToolComponent :: factory('Publisher', $this);
				break;
			case self :: ACTION_VIEW_LEARNING_PATH:
				$component = LearningPathToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_BROWSE_LEARNING_PATHS:
				$component = LearningPathToolComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_EXPORT_SCORM:
				$component = LearningPathToolComponent :: factory('ScormExporter', $this);
				break;
			default:
				$component = LearningPathToolComponent :: factory('Browser', $this);
				break;
		}
		
		$component->run();
		/*$trail = new BreadcrumbTrail();
		
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		if (isset($_GET['admin']))
		{
			$_SESSION['wikiadmin'] = $_GET['admin'];
		}
		if ($_SESSION['wikiadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			require_once dirname(__FILE__).'/../../learning_object_repo_viewer.class.php';
			$pub = new LearningObjectPublisher($this, 'learning_path');
			$html[] = '<p><a href="' . $this->get_url(array('admin' => 0), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
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
				echo '<p><a href="' . $this->get_url(array('admin' => 1), true) . '"><img src="'.Theme :: get_common_image_path().'action_publish.png" alt="'.Translation :: get('Publish').'" style="vertical-align:middle;"/> '.Translation :: get('Publish').'</a></p>';
			}
			echo $this->perform_requested_actions();
			$browser = new LearningPathBrowser($this);
			echo $browser->as_html();
			$this->display_footer();
		}*/
	}
	
	static function get_allowed_types()
	{
		return array('learning_path');
	}
}
?>