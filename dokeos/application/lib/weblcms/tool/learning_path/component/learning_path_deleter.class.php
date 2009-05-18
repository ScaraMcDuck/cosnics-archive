<?php

require_once dirname(__FILE__).'/../../../trackers/weblcms_lp_attempt_tracker.class.php';
require_once dirname(__FILE__).'/../../../trackers/weblcms_lpi_attempt_tracker.class.php';
require_once dirname(__FILE__).'/../../../trackers/weblcms_learning_path_assessment_attempts_tracker.class.php';
require_once dirname(__FILE__).'/../../../trackers/weblcms_learning_path_question_attempts_tracker.class.php';

class LearningPathToolDeleterComponent extends LearningPathToolComponent
{
	function run()
	{
        if($this->is_allowed(DELETE_RIGHT) /*&& !WikiTool :: is_wiki_locked(Request :: get(Tool :: PARAM_PUBLICATION_ID))*/)
		{
			if(isset($_GET[Tool :: PARAM_PUBLICATION_ID]))
				$publication_ids = $_GET[Tool :: PARAM_PUBLICATION_ID];
			else
				$publication_ids = $_POST[Tool :: PARAM_PUBLICATION_ID];

			if (!is_array($publication_ids))
			{
				$publication_ids = array ($publication_ids);
			}

			$datamanager = WeblcmsDataManager :: get_instance();

			foreach($publication_ids as $pid)
			{
				$publication = $datamanager->retrieve_learning_object_publication($pid);

				$condition = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $pid);
				$dummy = new WeblcmsLpAttemptTracker();
				$trackers = $dummy->retrieve_tracker_items($condition);
				foreach($trackers as $tracker)
					$tracker->delete();

				$publication->delete();
			}
			if(count($publication_ids) > 1)
			{
				$message = htmlentities(Translation :: get('LearningObjectPublicationsDeleted'));
			}
			else
			{
				$message = htmlentities(Translation :: get('LearningObjectPublicationDeleted'));
			}

			$this->redirect($message, '', array('pid' => null));
		}
	}

}
?>