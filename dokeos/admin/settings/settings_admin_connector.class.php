<?php
require_once Path :: get_admin_path() . 'lib/admin_data_manager.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_library_path() . 'filesystem/path.class.php';
require_once Path :: get_library_path() . 'filesystem/filesystem.class.php';

/**
 * Simple connector class to facilitate rendering settings forms by
 * preprocessing data from the datamanagers to a simple array format.
 * @author Hans De Bisschop
 */

class SettingsAdminConnector
{
	function get_languages()
	{
		$adm = AdminDataManager :: get_instance();
		$options = $adm->get_languages();
		
		return $options;
	}
	
	function get_themes()
	{
		$options = Theme :: get_themes();
		
		return $options;
	}
}
?>
