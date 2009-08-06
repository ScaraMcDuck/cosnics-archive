<?php
/**
 * @package application.assessment.assessment.component
 */
require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';

/**
 * Component to create a new assessment_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerAssessmentPublicationVisibilityChangerComponent extends AssessmentManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$pid = Request :: get('assessment_publication');
		if(!$pid)
		{
			$this->not_allowed();
			exit();
		}
		
		$publication = $this->retrieve_assessment_publication($pid);
		$publication->toggle_visibility();
		$succes = $publication->update();
		
		$message = $succes ? 'VisibilityChanged': 'VisibilityNotChanged';
		
		$this->redirect(Translation :: get($message), !$succes, array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS));
		
	}
}
?>