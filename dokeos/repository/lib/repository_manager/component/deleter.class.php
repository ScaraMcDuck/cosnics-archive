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
			$current_parent = $this->get_parameter(RepositoryManager :: PARAM_CATEGORY_ID);
			$failures = 0;
			foreach ($ids as $object_id)
			{
				$object = $this->get_parent()->retrieve_learning_object($object_id);
				// TODO: Roles & Rights.
				if ($object->get_owner_id() == $this->get_user_id())
				{
					if ($this->get_parent()->learning_object_deletion_allowed($object))
					{
						$object->delete();
						if ($current_parent && $current_parent == $object_id)
						{
							$current_parent = 0;
							$this->set_parameter(self :: PARAM_CATEGORY_ID, $this->get_root_category_id());
						}
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
					$message = 'SelectedObjectNotDeleted';
				}
				else
				{
					$message = 'NotAllSelectedObjectsDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedObjectDeleted';
				}
				else
				{
					$message = 'AllSelectedObjectsDeleted';
				}
			}
			$this->redirect(RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, get_lang($message));
		}
		else
		{
			$this->display_error_page(get_lang('NoObjectSelected'));
		}
	}
}
?>