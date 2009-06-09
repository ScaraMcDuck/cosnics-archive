<?php

require_once Path :: get_repository_path().'lib/learning_object_pub_feedback.class.php';

class ToolFeedbackDeleterComponent extends ToolComponent
{
    private $cid;
    private $pid;

    function run()
	{
        if($this->is_allowed(DELETE_RIGHT) /*&& !WikiTool :: is_wiki_locked(Request :: get(Tool :: PARAM_PUBLICATION_ID))*/)
		{
			if(Request :: get('fid'))
				$feedback_ids = Request :: get('fid');
			else
				$feedback_ids = $_POST['fid'];

            if(Request :: get('cid'))
				$this->cid = Request :: get('cid');
			else
				$this->cid = $_POST['cid'];

            if(Request :: get('pid'))
				$this->pid = Request :: get('pid');
			else
				$this->pid = $_POST['pid'];

			if (!is_array($feedback_ids))
			{
				$feedback_ids = array ($feedback_ids);
			}

			$datamanager = RepositoryDataManager :: get_instance();

			foreach($feedback_ids as $index => $fid)
			{
                $condition = new EqualityCondition(LearningObjectPubFeedback :: PROPERTY_FEEDBACK_ID, $fid);
                $feedbacks = $datamanager->retrieve_learning_object_pub_feedback($condition);
				while($feedback = $feedbacks->next_result())
                {
                    $feedback->delete();
                }
			}
			if(count($feedback_ids) > 1)
			{
				$message = htmlentities(Translation :: get('LearningObjectFeedbacksDeleted'));
			}
			else
			{
				$message = htmlentities(Translation :: get('LearningObjectFeedbackDeleted'));
			}

			$this->redirect($message, '', array(Tool :: PARAM_ACTION => 'discuss', 'pid' => $this->pid, 'cid' => $this->cid));
		}
	}

}
?>
