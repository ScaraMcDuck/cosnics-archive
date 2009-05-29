<?php
class ForumManagerToggleVisibilityComponent extends ForumManagerComponent
{
	function run()
	{
		if($this->is_allowed(DELETE_RIGHT))
		{
            if(isset($_GET[ForumManager::PARAM_FORUM_PUBLICATION]))
			{
				$publication_ids = $_GET[ForumManager::PARAM_FORUM_PUBLICATION];
			}
			else
			{
				$publication_ids = $_POST[ForumManager::PARAM_FORUM_PUBLICATION];
			}

			if (!is_array($publication_ids))
			{
				$publication_ids = array ($publication_ids);
			}

			$datamanager = ForumDataManager :: get_instance();

			foreach($publication_ids as $index => $pid)
			{
                $publication = $datamanager->retrieve_forum_publication($pid);

				if(isset($_GET[PARAM_VISIBILITY]))
				{
					$publication->set_hidden($_GET[PARAM_VISIBILITY]);
				}
				else
				{
					$publication->toggle_visibility();
				}

				$publication->update();
			}

			if(count($publication_ids) > 1)
			{
				$message = htmlentities(Translation :: get('LearningObjectPublicationsVisibilityChanged'));
			}
			else
			{
				$message = htmlentities(Translation :: get('LearningObjectPublicationVisibilityChanged'));
			}

			$params = array(ForumManager::PARAM_ACTION => ForumManager::ACTION_BROWSE);
//			if($_GET['details'] == 1)
//			{
//				$params['pid'] = $pid;
//				$params['tool_action'] = 'view';
//			}

			$this->redirect($message, '', $params);

			$this->redirect($message, false, $params);
		}
	}
}
?>
