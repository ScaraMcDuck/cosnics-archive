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

class BlockRepositoryConnector
{
	function get_rss_feed_objects()
	{
		$options = array();
		$rdm = RepositoryDataManager :: get_instance();
		$objects = $rdm->retrieve_learning_objects('rss_feed'); 
		
		while ($object = $objects->next_result())
		{
			$options[$object->get_id()] = $object->get_title();
		}
		
		return $options;
	}
}
?>
