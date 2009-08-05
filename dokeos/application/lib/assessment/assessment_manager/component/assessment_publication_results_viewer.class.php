<?php
/**
 * @package application.assessment.assessment.component
 */
require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';

/**
 * Component to create a new assessment_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerAssessmentPublicationResultsViewerComponent extends AssessmentManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewResults')));

		$pid = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);
		
		if(!$pid)
		{
			$html = $this->display_summary_results();
		}
		else 
		{
			$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $pid)), Translation :: get('ViewAssessmentResults')));
			$html = $this->display_assessment_results($pid);
		}
		
		$this->display_header($trail);
		echo $html;
		$this->display_footer();
	}
	
	function display_summary_results()
	{
		require_once(Path :: get_application_path() . 'lib/assessment/reporting/templates/assessment_attempts_summary_template.class.php');
		
		$current_category = Request :: get('category');
		$current_category = $current_category ? $current_category : 0;
		
		$parameters = array('category'  => $current_category);
		$template = new AssessmentAttemptsSummaryTemplate($this, 0, $parameters, null);
		$template->set_reporting_blocks_function_parameters($parameters);
		return $template->to_html();
	}
	
	function display_assessment_results($pid)
	{
		require_once(Path :: get_application_path() . 'lib/assessment/reporting/templates/assessment_attempts_template.class.php');
		
		$parameters = array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $pid);
		$template = new AssessmentAttemptsTemplate($this, 0, $parameters, null, $pid);
		$template->set_reporting_blocks_function_parameters($parameters);
		return $template->to_html();
	}
}
?>