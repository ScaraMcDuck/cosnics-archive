<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerDeleterComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
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
			$delete_version = $_GET[RepositoryManager :: PARAM_DELETE_VERSION];
			$permanent = $_GET[RepositoryManager :: PARAM_DELETE_PERMANENTLY];
			$recycled = $_GET[RepositoryManager :: PARAM_DELETE_RECYCLED];
			foreach ($ids as $object_id)
			{
				$object = $this->get_parent()->retrieve_learning_object($object_id);
				// TODO: Roles & Rights.
				if ($object->get_owner_id() == $this->get_user_id())
				{
					if ($delete_version)
					{
						if ($this->get_parent()->learning_object_deletion_allowed($object, 'version'))
						{
							$object->delete_version();
						}
						else
						{
							$failures ++;
						}
					}
					else
					{
						if ($this->get_parent()->learning_object_deletion_allowed($object))
						{
							if ($permanent)
							{
								$versions = $object->get_learning_object_versions();
								foreach ($versions as $version)
								{
									$version->delete();
								}
							}
							elseif ($recycled)
							{
								$versions = $object->get_learning_object_versions();
								foreach ($versions as $version)
								{
									$version->set_state(LearningObject :: STATE_RECYCLED);
									$version->update();
								}
							}
						}
						else
						{
							$failures ++;
						}
					}
				}
				else
				{
					$failures ++;
				}
			}

			if ($delete_version)
			{
				if ($failures)
				{
					$message = 'SelectedVersionNotDeleted';
				}
				else
				{
					$message = 'SelectedVersionDeleted';
				}
			}
			else
			{
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
			}

			$parameters = array();
			$parameters[RepositoryManager :: PARAM_ACTION] = ($permanent ? RepositoryManager :: ACTION_BROWSE_RECYCLED_LEARNING_OBJECTS : RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS);

			$this->redirect(Translation :: get($message), ($failures ? true : false), $parameters);
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>