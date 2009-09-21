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
class RepositoryManagerTemplateDeleterComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = Request :: get(RepositoryManager :: PARAM_LEARNING_OBJECT_ID);
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $lo_id)
			{
				$lo = $this->retrieve_learning_object($lo_id);
				
				if(!$lo->delete())
				/*{
					//$failures++;
				}*/
				
				$or_conditions = array();
				$or_conditions[] = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $lo_id, ComplexLearningObjectItem :: get_table_name());
				$or_conditions[] = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $lo_id);
				
				$condition = new OrCondition($or_conditions);
				$clois = $this->retrieve_complex_learning_object_items($condition);
				while($cloi = $clois->next_result())
				{
					$cloi->delete();
				}
			}
	
			if ($failures > 0)
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

			$this->redirect(Translation :: get($message), ($failures > 0), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_TEMPLATES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>