<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';

class RepositoryManagerRestorerComponent extends RepositoryManagerComponent
{
	function run()
	{
		$ids = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];
		if (!empty($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			$failures = 0;
			foreach ($ids as $object_id)
			{
				$object = $this->get_parent()->retrieve_learning_object($object_id);
				// TODO: Roles & Rights.
				if ($object->get_owner_id() == $this->get_user_id())
				{
					if ($object->get_state() == LearningObject :: STATE_RECYCLED)
					{
						$object->set_state(LearningObject :: STATE_NORMAL);
						$object->update();
					}
					else
					{
						$failures++;
					}
				}
				else
				{
					$failures++;
				}
			}
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedObjectNotRestored';
				}
				else
				{
					$message = 'NotAllSelectedObjectsRestored';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedObjectRestored';
				}
				else
				{
					$message = 'AllSelectedObjectsRestored';
				}
			}
			$this->redirect(RepositoryManager :: ACTION_BROWSE_RECYCLED_LEARNING_OBJECTS, get_lang($message));
		}
		else
		{
			$this->display_error_page(get_lang('NoObjectSelected'));
		}
	}
}
?>