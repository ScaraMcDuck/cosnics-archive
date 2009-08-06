<?php
/**
 * @package application.assessment.assessment.component
 */
require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/assessment_publication_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

/**
 * Component to edit an existing assessment_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerUpdaterComponent extends AssessmentManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateAssessmentPublication')));

		$publication = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);

		if(isset($publication))
		{
			$assessment_publication = $this->retrieve_assessment_publication($publication);
			$learning_object = $assessment_publication->get_publication_object();
			
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $learning_object, 'edit', 'post', $this->get_url(array(Application :: PARAM_ACTION => AssessmentManager :: ACTION_EDIT_ASSESSMENT_PUBLICATION, AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $publication)));
			
			if( $form->validate() || Request :: get('validated'))
			{
				if(!Request :: get('validated'))
				{
					$form->update_learning_object();
				}

				if($form->is_version())
				{
					$assessment_publication->set_learning_object($learning_object->get_latest_version());
					$assessment_publication->update();
				}
			
				$publication_form = new AssessmentPublicationForm(AssessmentPublicationForm :: TYPE_SINGLE, $learning_object, $this->get_user(), $this->get_url(array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $publication, 'validated' => 1)));
				$publication_form->set_publication($assessment_publication);
	
				if( $publication_form->validate())
				{
					$success = $publication_form->update_learning_object_publication();
					$message = ($success ? 'LearningObjectUpdated' : 'LearningObjectNotUpdated');
	
					$this->redirect(Translation :: get($message), !$success, array(Application :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS), array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION));
				}
				else
				{
					$this->display_header($trail, true);
					$publication_form->display();
					$this->display_footer();
				}
			}
			else
			{
				$this->display_header($trail, true);
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
		}
	}
}
?>