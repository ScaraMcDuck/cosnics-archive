<?php
/**
 * @package application.weblcms.tool.exercise.component
 */
require_once dirname(__FILE__).'/../../../learning_object_publisher.class.php';

class ExerciseToolPublisherComponent extends ExerciseToolComponent 
{
	function run() 
	{
		if (!$this->is_allowed(ADD_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();
		$pub = new LearningObjectPublisher($this, 'exercise', true);
		
		//$html[] = '<p><a href="' . $this->get_url(array('admin' => 0), true) . '"><img src="'.Theme :: get_common_img_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
		$html[] = '<p><a href="' . $this->get_url(array(ExerciseTool :: PARAM_ACTION => ExerciseTool :: ACTION_VIEW_EXERCISES), true) . '"><img src="'.Theme :: get_common_img_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
		$html[] =  $pub->as_html();
		
		$this->display_header($trail);
		echo implode("\n",$html);
		$this->display_footer();
	}
}

?>