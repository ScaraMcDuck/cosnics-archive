<?php
/**
 * @package application.assessment.assessment.component
 */
require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/assessment_publication_group_form.class.php';

/**
 * Component to edit an existing assessment_publication_group object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerAssessmentPublicationGroupUpdaterComponent extends AssessmentManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE)), Translation :: get('BrowseAssessment')));
		$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATION_GROUPS)), Translation :: get('BrowseAssessmentPublicationGroups')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateAssessmentPublicationGroup')));

		$assessment_publication_group = $this->retrieve_assessment_publication_group(Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION_GROUP));
		$form = new AssessmentPublicationGroupForm(AssessmentPublicationGroupForm :: TYPE_EDIT, $assessment_publication_group, $this->get_url(array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION_GROUP => $assessment_publication_group->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_assessment_publication_group();
			$this->redirect($success ? Translation :: get('AssessmentPublicationGroupUpdated') : Translation :: get('AssessmentPublicationGroupNotUpdated'), !$success, array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATION_GROUPS));
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