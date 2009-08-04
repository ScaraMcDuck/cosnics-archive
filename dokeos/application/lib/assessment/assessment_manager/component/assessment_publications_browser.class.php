<?php
/**
 * @package application.assessment.assessment.component
 */

require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';
require_once dirname(__FILE__).'/assessment_publication_browser/assessment_publication_browser_table.class.php';

/**
 * assessment component which allows the user to browse his assessment_publications
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerAssessmentPublicationsBrowserComponent extends AssessmentManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseAssessmentPublications')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_assessment_publication_url() . '">' . Translation :: get('CreateAssessmentPublication') . '</a>';
		echo '<br /><br />';
		echo $this->get_table();
		$this->display_footer();
	}

	function get_table()
	{
		$table = new AssessmentPublicationBrowserTable($this, array(Application :: PARAM_APPLICATION => 'assessment', Application :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS), null);
		return $table->as_html();
	}

}
?>