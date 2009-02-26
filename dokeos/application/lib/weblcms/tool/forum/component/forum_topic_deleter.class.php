<?php

class ForumToolTopicDeleterComponent extends ForumToolComponent
{
	function run()
	{
		if($this->is_allowed(DELETE_RIGHT))
		{ 
			$forum = Request :: get('forum');
			$topics = Request :: get('topic');
			$pid = Request :: get(Tool :: PARAM_PUBLICATION_ID); 
			
			$posts = Request :: get('post');
				
			if (!is_array($topics))
			{
				$topics = array ($topics);
			}
			
			$datamanager = RepositoryDataManager :: get_instance();
			$params = array(Tool :: PARAM_ACTION => 'view', 'pid' => $pid);
			
			foreach($topics as $topic)
			{
				$cloi = $datamanager->retrieve_complex_learning_object_item($topic);
				$cloi->delete();
			}
			if(count($topics) > 1)
			{
				$message = htmlentities(Translation :: get('ForumTopicsDeleted'));
			}
			else
			{
				$message = htmlentities(Translation :: get('ForumTopicDeleted'));
			}
			
			$this->redirect(null, $message, false, $params);
		}
	}

}
?>