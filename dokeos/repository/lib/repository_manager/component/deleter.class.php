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
					if ($this->get_parent()->learning_object_deletion_allowed($object))
					{
						//$object->delete();
						$object->set_state(LearningObject :: STATE_RECYCLED);
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
			$dont_change_category = false;
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedObjectNotMovedToRecycleBin';
					$dont_change_category = true;
				}
				else
				{
					$message = 'NotAllSelectedObjectsMovedToRecycleBin';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedObjectMovedToRecycleBin';
				}
				else
				{
					$message = 'AllSelectedObjectsMovedToRecycleBin';
				}
			}
			$this->redirect(($dont_change_category ? RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS : RepositoryManager :: ACTION_BROWSE_RECYCLED_LEARNING_OBJECTS), get_lang($message));
		}
		else
		{
			$this->display_error_page(get_lang('NoObjectSelected'));
		}
	}
}
?>