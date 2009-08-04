<?php
/**
 * @package application.assessment.assessment.component
 */
require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';

/**
 * Assessment component which allows the user to browse the assessment application
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerBrowserComponent extends AssessmentManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseAssessment')));

		$this->display_header($trail);

		echo '<br /><a href="' . $this->get_browse_assessment_publications_url() . '">' . Translation :: get('BrowseAssessmentPublications') . '</a>';
		echo '<br /><a href="' . $this->get_browse_assessment_publication_groups_url() . '">' . Translation :: get('BrowseAssessmentPublicationGroups') . '</a>';
		echo '<br /><a href="' . $this->get_browse_assessment_publication_users_url() . '">' . Translation :: get('BrowseAssessmentPublicationUsers') . '</a>';

		$this->display_footer();
	}

}
?>