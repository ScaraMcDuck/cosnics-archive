<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
/**
 * Repository manager component to restore learning objects. This means movig
 * learning objects from the recycle bin to there original location.
 */
class RepositoryManagerRestorerComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
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
						$versions = $object->get_learning_object_versions();
						foreach ($versions as $version)
						{
							$version->set_state(LearningObject :: STATE_NORMAL);
							$version->update();
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

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_RECYCLED_LEARNING_OBJECTS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>