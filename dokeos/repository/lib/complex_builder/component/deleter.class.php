<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../complex_builder.class.php';
require_once dirname(__FILE__).'/../complex_builder_component.class.php';
/**
 */
class ComplexBuilderDeleterComponent extends ComplexBuilderComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[ComplexBuilder :: PARAM_CLOI_ID];
		$root = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
	
		$failures = 0;
		$parent = 0;
		
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			$rdm = RepositoryDataManager :: get_instance();
			
			foreach ($ids as $cloi_id)
			{
				$cloi = $rdm->retrieve_complex_learning_object_item($cloi_id);
				if($parent == 0) $parent = $cloi->get_parent();

				if ($cloi->get_user_id() == $this->get_user_id())
				{
					// TODO: check if deletion is allowed
					//if ($this->get_parent()->complex_learning_object_item_deletion_allowed($cloi))
					{
						if(!$cloi->delete())
						{
							$failures++;
						}
					}
				}
				else
				{
					$failures ++;
				}
			}
			
			if($parent == $root)
				$parent = null;
			
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
			
			$this->redirect(Translation :: get($message), false, array('go' => 'build_complex', ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, ComplexBuilder :: PARAM_CLOI_ID => $parent, ComplexBuilder :: PARAM_ROOT_LO => $root, 'publish' => Request :: get('publish')));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>