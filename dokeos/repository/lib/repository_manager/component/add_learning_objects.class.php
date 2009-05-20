<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerAddLearningObjectsComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[RepositoryManager :: PARAM_CLOI_REF];
		$root = $_GET[RepositoryManager :: PARAM_CLOI_ROOT_ID];
		$parent = $_GET[RepositoryManager :: PARAM_CLOI_ID];

		$failures = 0;

		//echo($root . ' ' . $parent);
		if (!empty ($ids) && isset($root) && isset($parent))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $ref)
			{
				$type = RepositoryDataManager :: get_instance()->determine_learning_object_type($ref);

				$cloi = ComplexLearningObjectItem :: factory($type);

				$cloi->set_ref($ref);
				$cloi->set_user_id($this->get_user()->get_id());
				$cloi->set_parent($parent);
				$cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($parent));

				if(!$cloi->create())
				{
					$failures++;
				}

			}

			if ($failures)
				{
					if (count($ids) == 1)
					{
						$message = 'SelectedObjectNotAdded';
					}
					else
					{
						$message = 'NotAllSelectedObjectsAdded';
					}
				}
				else
				{
					if (count($ids) == 1)
					{
						$message = 'SelectedObjectAdded';
					}
					else
					{
						$message = 'AllSelectedObjectsAdded';
					}
				}

			$parameters = array();
			$parameters[RepositoryManager :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS;
			$parameters[RepositoryManager :: PARAM_CLOI_ID] = $parent;
			$parameters[RepositoryManager :: PARAM_CLOI_ROOT_ID] = $root;
			$parameters['publish'] = $_GET['publish'];

			$this->redirect(Translation :: get($message), false, $parameters);
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>