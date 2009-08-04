<?php
/**
 * @package application.assessment.assessment.component
 */
require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';

/**
 * Component to delete assessment_publication_users objects
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerAssessmentPublicationUserDeleterComponent extends AssessmentManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION_USER];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$assessment_publication_user = $this->retrieve_assessment_publication_user($id);

				if (!$assessment_publication_user->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedAssessmentPublicationUserDeleted';
				}
				else
				{
					$message = 'SelectedAssessmentPublicationUserDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedAssessmentPublicationUsersDeleted';
				}
				else
				{
					$message = 'SelectedAssessmentPublicationUsersDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATION_USERS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoAssessmentPublicationUsersSelected')));
		}
	}
}
?>