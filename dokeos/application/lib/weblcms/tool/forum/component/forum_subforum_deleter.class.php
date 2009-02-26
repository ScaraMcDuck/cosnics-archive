<?php

class ForumToolSubforumDeleterComponent extends ForumToolComponent
{
	function run()
	{
		if($this->is_allowed(DELETE_RIGHT))
		{ 
			$forum = Request :: get('forum');
			$subforums = Request :: get('subforum');
			$pid = Request :: get(Tool :: PARAM_PUBLICATION_ID); 
				
			if (!is_array($subforums))
			{
				$subforums = array ($subforums);
			}
			
			$datamanager = RepositoryDataManager :: get_instance();
			$params = array(Tool :: PARAM_ACTION => 'view', 'pid' => $pid);
			
			foreach($subforums as $subforum)
			{
				$cloi = $datamanager->retrieve_complex_learning_object_item($subforum);
				$cloi->delete();
			}
			if(count($subforums) > 1)
			{
				$message = htmlentities(Translation :: get('SubforumsDeleted'));
			}
			else
			{
				$message = htmlentities(Translation :: get('SubforumDeleted'));
			}
			
			$this->redirect(null, $message, false, $params);
		}
	}

}
?>