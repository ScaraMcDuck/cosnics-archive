<?php
/**
 * @package application.assessment.assessment.component
 */
require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/assessment_publication_form.class.php';

/**
 * Component to create a new assessment_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerAssessmentPublicationCreatorComponent extends AssessmentManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateAssessmentPublication')));

		$assessment_publication = new AssessmentPublication();
		$form = new AssessmentPublicationForm(AssessmentPublicationForm :: TYPE_CREATE, $assessment_publication, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_assessment_publication();
			$this->redirect($success ? Translation :: get('AssessmentPublicationCreated') : Translation :: get('AssessmentPublicationNotCreated'), !$success, array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS));
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