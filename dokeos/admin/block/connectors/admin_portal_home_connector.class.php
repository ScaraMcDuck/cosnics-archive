<?php
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_library_path() . 'filesystem/path.class.php';
require_once Path :: get_library_path() . 'filesystem/filesystem.class.php';

/**
 * Simple connector class to facilitate rendering settings forms by
 * preprocessing data from the datamanagers to a simple array format.
 * @author Hans De Bisschop
 */

class BlockAdminConnector
{
	function get_portal_home_objects()
	{
		$options = array();
		$rdm = RepositoryDataManager :: get_instance();
		$objects = $rdm->retrieve_learning_objects('portal_home'); 
		
		if($objects->size() == 0)
		{
			$options[0] = Translation :: get('CreatePortalHomeFirst');
		}
		else
		{
			while ($object = $objects->next_result())
			{
				$options[$object->get_id()] = $object->get_title();
			}
		}
		
		return $options;
	}
}
?>
