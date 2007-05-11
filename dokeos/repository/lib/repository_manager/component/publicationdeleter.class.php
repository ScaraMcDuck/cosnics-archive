<?php
/**
 * @package repository.repositorymanager
 * 
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object publication from the publication overview.
 */
class RepositoryManagerPublicationDeleterComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$id = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];
		if (!empty ($id))
		{
			$failures = 0;

			$object = $this->get_parent()->retrieve_learning_object($id);
			// TODO: Roles & Rights.
			if ($object->get_owner_id() == $this->get_user_id())
			{
				$versions = $object->get_learning_object_versions();
				
				foreach ($versions as $version)
				{
					if (!$version->delete_links())
					{
						$failures++;
					}
				}
			}
			else
			{
				$failures ++;
			}
			
			// TODO: SCARA - Structurize + cleanup (possible) failures
			

			if ($failures)
			{
				if ($failures >= 1)
				{
					$message = 'SelectedObjectNotUnlinked';
				}
				else
				{
					$message = 'NotAllVersionsUnlinked';
				}
			}
			else
			{
				$message = 'SelectedObjectUnlinked';
			}
			$this->redirect(RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, get_lang($message));
		}
		else
		{
			$this->display_error_page(htmlentities(get_lang('NoObjectSelected')));
		}
	}
}
?>