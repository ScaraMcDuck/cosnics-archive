<?php

class ForumToolPostDeleterComponent extends ForumToolComponent
{
	function run()
	{
		if($this->is_allowed(DELETE_RIGHT))
		{ 
			$cid = Request :: get(Tool :: PARAM_COMPLEX_ID);
			$pid = Request :: get(Tool :: PARAM_PUBLICATION_ID); 
			
			$posts = Request :: get('post');
				
			if (!is_array($posts))
			{
				$posts = array ($posts);
			}
			
			$datamanager = RepositoryDataManager :: get_instance();
			
			foreach($posts as $index => $post)
			{
				$cloi = $datamanager->retrieve_complex_learning_object_item($post);
				$cloi->delete();
			}
			if(count($cloi_ids) > 1)
			{
				$message = htmlentities(Translation :: get('ForumPostDeleted'));
			}
			else
			{
				$message = htmlentities(Translation :: get('ForumPostNotDeleted'));
			}
			
			$this->redirect(null, $message, false, array(Tool :: PARAM_ACTION => 'view_topic', 'pid' => $pid, 'cid' => $cid));
		}
	}

}
?>