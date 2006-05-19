<?php

/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerDeleterComponent extends RepositoryManagerComponent
{
	function run()
	{
		$ids = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			$failures = 0;
			$permanent = $_GET[RepositoryManager :: PARAM_DELETE_PERMANENTLY];
			foreach ($ids as $object_id)
			{
				$object = $this->get_parent()->retrieve_learning_object($object_id);
				// TODO: Roles & Rights.
				if ($object->get_owner_id() == $this->get_user_id())
				{
					if ($this->get_parent()->learning_object_deletion_allowed($object))
					{
						if ($permanent)
						{
							$object->delete();
						}
						else
						{
							$object->set_state(LearningObject :: STATE_RECYCLED);
							$object->update();
						}
					}
					else
					{
						$failures ++;
					}
				}
				else
				{
					$failures ++;
				}
			}
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedObjectNot'. ($permanent ? 'Deleted' : 'MovedToRecycleBin');
				}
				else
				{
					$message = 'NotAllSelectedObjects'. ($permanent ? 'Deleted' : 'MovedToRecycleBin');
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedObject'. ($permanent ? 'Deleted' : 'MovedToRecycleBin');
				}
				else
				{
					$message = 'AllSelectedObjects'. ($permanent ? 'Deleted' : 'MovedToRecycleBin');
				}
			}
			$this->redirect(($permanent ? RepositoryManager :: ACTION_BROWSE_RECYCLED_LEARNING_OBJECTS : RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS), get_lang($message));
		}
		else
		{
			$this->display_error_page(htmlentities(get_lang('NoObjectSelected')));
		}
	}
}
?>