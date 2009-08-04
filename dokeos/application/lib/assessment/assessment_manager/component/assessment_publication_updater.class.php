<?php
/**
 * @package application.assessment.assessment.component
 */
require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/assessment_publication_form.class.php';

/**
 * Component to edit an existing assessment_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerAssessmentPublicationUpdaterComponent extends AssessmentManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateAssessmentPublication')));

		$assessment_publication = $this->retrieve_assessment_publication(Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION));
		$form = new AssessmentPublicationForm(AssessmentPublicationForm :: TYPE_EDIT, $assessment_publication, $this->get_url(array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_assessment_publication();
			$this->redirect($success ? Translation :: get('AssessmentPublicationUpdated') : Translation :: get('AssessmentPublicationNotUpdated'), !$success, array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS));
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