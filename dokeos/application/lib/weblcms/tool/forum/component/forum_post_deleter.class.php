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
			$params = array(Tool :: PARAM_ACTION => 'view_topic', 'pid' => $pid, 'cid' => $cid);
			
			foreach($posts as $index => $post)
			{
				$cloi = $datamanager->retrieve_complex_learning_object_item($post);
				$cloi->delete();
				
				$siblings = $datamanager->count_complex_learning_object_items(new EqualityCondition('parent', $cloi->get_parent()));
				if($siblings == 0)
				{
					/*$wrappers = $datamanager->retrieve_complex_learning_object_items(new EqualityCondition('ref', $cloi->get_parent()));
					while($wrapper = $wrappers->next_result())
					{
						$wrapper->delete();
					}
					
					$datamanager->delete_learning_object_by_id($cloi->get_parent());*/
					
					$params[Tool :: PARAM_ACTION] = 'view';
					$params['cid'] = null;
				}
			}
			if(count($posts) > 1)
			{
				$message = htmlentities(Translation :: get('ForumPostsDeleted'));
			}
			else
			{
				$message = htmlentities(Translation :: get('ForumPostDeleted'));
			}
			
			$this->redirect(null, $message, false, $params);
		}
	}

}
?>