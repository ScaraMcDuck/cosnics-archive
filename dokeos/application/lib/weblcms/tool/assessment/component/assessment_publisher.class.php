<?php
/**
 * @package application.weblcms.tool.assessment.component
 */
require_once dirname(__FILE__) . '/../../../learning_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';
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
			Display :: not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();
		$trail->add(new BreadCrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH)), Translation :: get('PublishAssessment')));
		
		$object = $_GET['object'];
		//$edit = $_GET['edit'];

		$pub = new LearningObjectRepoViewer($this, array('assessment', 'survey', 'hotpotatoes'), true, RepoViewer :: SELECT_MULTIPLE);
		
		//dump($object);
		if(!isset($object)) // || $edit = 1)
		{	
			$html[] =  $pub->as_html();
		}
		else
		{
			$publisher = new LearningObjectPublisher($pub);
			$html[] = $publisher->get_publications_form($object);
		}
		
		$this->display_header($trail);
		
		echo implode("\n",$html);
		$this->display_footer();
	}
}

?>