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
class AssessmentToolRepoviewerComponent extends AssessmentToolComponent
{
	/**
	 * Shows the html for this component.
	 *
	 */
	function run()
	{
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$redirect_params = $_SESSION['redirect_params'];
		$types = Request :: get(AssessmentTool :: PARAM_REPO_TYPES);
		$trail = new BreadcrumbTrail();
		$trail->add(new BreadCrumb($this->get_url($redirect_params), Translation :: get('PreviousPage')));
		$trail->add(new BreadCrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_REPOVIEWER, AssessmentTool :: PARAM_REPO_TYPES => $types)), Translation :: get('Repoviewer')));
		$trail->add_help('courses assessment tool');

		$object = Request :: get('object');

		$pub = new LearningObjectRepoViewer($this, $types, true, RepoViewer :: SELECT_MULTIPLE, AssessmentTool :: ACTION_REPOVIEWER);
		//$pub->set_parameter(AssessmentTool :: PARAM_ACTION, AssessmentTool :: ACTION_REPOVIEWER);
		$pub->set_parameter(AssessmentTool :: PARAM_REPO_TYPES, $types);

		if(!isset($object))
		{
			$html[] = '<p><a href="' . $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_ASSESSMENTS), array(), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			//redirect
			$redirect_params = $_SESSION['redirect_params'];
			$redirect_params['object'] = $object;
			$this->redirect(null, false, $redirect_params);
		}

		$this->display_header($trail, true);

		echo implode("\n",$html);
		$this->display_footer();
	}
}

?>