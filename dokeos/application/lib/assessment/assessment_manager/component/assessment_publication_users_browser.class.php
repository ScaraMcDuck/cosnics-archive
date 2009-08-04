<?php
/**
 * @package application.assessment.assessment.component
 */

require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';

/**
 * assessment component which allows the user to browse his assessment_publication_users
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerAssessmentPublicationUsersBrowserComponent extends AssessmentManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE)), Translation :: get('BrowseAssessment')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseAssessmentPublicationUsers')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_assessment_publication_user_url() . '">' . Translation :: get('CreateAssessmentPublicationUser') . '</a>';
		echo '<br /><br />';

		$assessment_publication_users = $this->retrieve_assessment_publication_users();
		while($assessment_publication_user = $assessment_publication_users->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($assessment_publication_user);
			echo '<br /><a href="' . $this->get_update_assessment_publication_user_url($assessment_publication_user). '">' . Translation :: get('UpdateAssessmentPublicationUser') . '</a>';
			echo ' | <a href="' . $this->get_delete_assessment_publication_user_url($assessment_publication_user) . '">' . Translation :: get('DeleteAssessmentPublicationUser') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>