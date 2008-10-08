<?php
/**
 * @package application.weblcms.tool.exercise.component
 */
require_once dirname(__FILE__).'/../../../learning_object_publisher.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';

/**
 * Represents the publisher component for the exercise tool.
 */
class ExerciseToolPublisherComponent extends ExerciseToolComponent 
{
	/**
	 * Shows the html for this component.
	 *
	 */
	function run() 
	{
		if (!$this->is_allowed(ADD_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();
		$pub = new LearningObjectPublisher($this, 'exercise', true);
		
		$html[] = '<a href="' . $this->get_url(array(ExerciseTool :: PARAM_ACTION => ExerciseTool :: ACTION_VIEW_EXERCISES), true) . '"><img src="'.Theme :: get_common_img_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a>';
		$html[] =  $pub->as_html();
		
		$this->display_header($trail);
		
		echo implode("\n",$html);
		$this->display_footer();
	}
}

?>