<?php
/**
 * @package application.assessment.assessment.component
 */
require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/assessment_publication_user_form.class.php';

/**
 * Component to create a new assessment_publication_user object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerAssessmentPublicationUserCreatorComponent extends AssessmentManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE)), Translation :: get('BrowseAssessment')));
		$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATION_USERS)), Translation :: get('BrowseAssessmentPublicationUsers')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateAssessmentPublicationUser')));

		$assessment_publication_user = new AssessmentPublicationUser();
		$form = new AssessmentPublicationUserForm(AssessmentPublicationUserForm :: TYPE_CREATE, $assessment_publication_user, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_assessment_publication_user();
			$this->redirect($success ? Translation :: get('AssessmentPublicationUserCreated') : Translation :: get('AssessmentPublicationUserNotCreated'), !$success, array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATION_USERS));
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