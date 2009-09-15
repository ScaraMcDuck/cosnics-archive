<?php
/**
 * @package application.weblcms.tool.assessment.component
 */
require_once dirname(__FILE__) . '/../../../learning_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/complex_builder/complex_builder.class.php';

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
		$trail->add_help('courses assessment tool');

		$pub = new LearningObjectRepoViewer($this, array('assessment', 'survey', 'hotpotatoes'), true, RepoViewer :: SELECT_MULTIPLE);

		if(!$pub->any_object_selected())
		{
			$this->display_header($trail, true);
			echo $pub->as_html();
			$this->display_footer();
		}
		else
		{
			$object_id = Request :: get('object');
			
			$publisher = new LearningObjectPublisher($pub);
			$this->display_header($trail, true);
			echo $publisher->get_publications_form($object_id);
			$this->display_footer();
		}
	}
}

?>