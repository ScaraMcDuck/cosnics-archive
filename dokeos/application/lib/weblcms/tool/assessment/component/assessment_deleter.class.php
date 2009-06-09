<?php
require_once Path :: get_application_path().'lib/weblcms/trackers/weblcms_assessment_attempts_tracker.class.php';

class AssessmentToolDeleterComponent extends AssessmentToolComponent
{
	function run()
	{
		if($this->is_allowed(DELETE_RIGHT))
		{ 
			if(Request :: get(Tool :: PARAM_PUBLICATION_ID))
				$publication_ids = Request :: get(Tool :: PARAM_PUBLICATION_ID);
			else
				$publication_ids = $_POST[Tool :: PARAM_PUBLICATION_ID]; 
			
			if (!is_array($publication_ids))
			{
				$publication_ids = array ($publication_ids);
			}
			
			$this->delete_publications($publication_ids);
		}
	}
	
	function delete_publications($publication_ids)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$success = true;
		foreach($publication_ids as $index => $pid)
		{
			$track = new WeblcmsAssessmentAttemptsTracker();
			$condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $pid);
			$items = $track->retrieve_tracker_items();
			
			if (count($items) == 0)
			{
				$publication = $datamanager->retrieve_learning_object_publication($pid); 
				$publication->delete();
			}
			else
			{
				$success = false;
			}
		}
		
		if(count($publication_ids) > 1)
		{
			if ($success)
				$message = htmlentities(Translation :: get('LearningObjectPublicationsDeleted'));
			else
				$message = htmlentities(Translation :: get('SomePublicationsCouldNotBeDeleted'));
		}
		else
		{
			if ($success)
				$message = htmlentities(Translation :: get('LearningObjectPublicationDeleted'));
			else
				$message = htmlentities(Translation :: get('LearningObjectPublicationNotDeleted'));
				
		}
		
		$this->redirect($message, (!$success), array('pid' => null));
	}
}
?>