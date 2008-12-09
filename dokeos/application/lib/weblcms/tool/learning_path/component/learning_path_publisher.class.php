<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';

class LearningPathToolPublisherComponent extends LearningPathToolComponent
{
	function run() 
	{
		if (!$this->is_allowed(ADD_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();
		$pub = new LearningObjectPublisher($this, 'learning_path', true);
		
		$html[] = '<a href="' . $this->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATHS), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a>';
		$html[] =  $pub->as_html();
		
		$this->display_header($trail);
		
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>