<?php
/**
 * @package application.assessment.assessment.component
 */
require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/assessment_publication_user_form.class.php';

/**
 * Component to edit an existing assessment_publication_user object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerAssessmentPublicationUserUpdaterComponent extends AssessmentManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE)), Translation :: get('BrowseAssessment')));
		$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATION_USERS)), Translation :: get('BrowseAssessmentPublicationUsers')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateAssessmentPublicationUser')));

		$assessment_publication_user = $this->retrieve_assessment_publication_user(Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION_USER));
		$form = new AssessmentPublicationUserForm(AssessmentPublicationUserForm :: TYPE_EDIT, $assessment_publication_user, $this->get_url(array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION_USER => $assessment_publication_user->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_assessment_publication_user();
			$this->redirect($success ? Translation :: get('AssessmentPublicationUserUpdated') : Translation :: get('AssessmentPublicationUserNotUpdated'), !$success, array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATION_USERS));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>