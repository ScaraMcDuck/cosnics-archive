<?php
class ToolToggleVisibilityComponent extends ToolComponent
{
	function run()
	{
		if($this->is_allowed(DELETE_RIGHT))
		{
			if(isset($_GET[Tool :: PARAM_PUBLICATION_ID]))
			{
				$publication_ids = $_GET[Tool :: PARAM_PUBLICATION_ID];
			}
			else
			{
				$publication_ids = $_POST[Tool :: PARAM_PUBLICATION_ID];
			}

			if (!is_array($publication_ids))
			{
				$publication_ids = array ($publication_ids);
			}

			$datamanager = WeblcmsDataManager :: get_instance();

			foreach($publication_ids as $index => $pid)
			{
				$publication = $datamanager->retrieve_learning_object_publication($pid);

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

			$params = array();
			if($_GET['details'] == 1)
			{
				$params['pid'] = $pid;
				$params['tool_action'] = 'view';
			}

			$this->redirect($message, '', $params);

			$this->redirect($message, false, $params);
		}
	}
}
?>
