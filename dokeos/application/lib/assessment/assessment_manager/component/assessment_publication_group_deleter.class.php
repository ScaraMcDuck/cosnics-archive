<?php
/**
 * @package application.assessment.assessment.component
 */
require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';

/**
 * Component to delete assessment_publication_groups objects
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerAssessmentPublicationGroupDeleterComponent extends AssessmentManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION_GROUP];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$assessment_publication_group = $this->retrieve_assessment_publication_group($id);

				if (!$assessment_publication_group->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedAssessmentPublicationGroupDeleted';
				}
				else
				{
					$message = 'SelectedAssessmentPublicationGroupDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedAssessmentPublicationGroupsDeleted';
				}
				else
				{
					$message = 'SelectedAssessmentPublicationGroupsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATION_GROUPS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoAssessmentPublicationGroupsSelected')));
		}
	}
}
?>