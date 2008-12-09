<?php
/**
 * @package application.weblcms.tool.assessment.component
 */
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';

/**
 * Represents the repo_viewer component for the assessment tool.
 */
class AssessmentToolPublisherComponent extends AssessmentToolComponent 
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
		/*$pub = new LearningObjectPublisher($this, 'assessment', true);
		
		$html[] = '<a href="' . $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_ASSESSMENTS), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a>';
		$html[] =  $pub->as_html();*/
		
		$object = $_GET['object'];
		
		$pub = new LearningObjectRepoViewer($this, 'assessment', true);
		
		if(!isset($object))
		{	
			$html[] = '<p><a href="' . $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_ASSESSMENTS), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			//$html[] = 'LearningObject: ';
			$publisher = new LearningObjectPublisher($pub);
			$html[] = $publisher->get_publications_form($object);
		}
		
		$this->display_header($trail);
		
		echo implode("\n",$html);
		$this->display_footer();
	}
}

?>