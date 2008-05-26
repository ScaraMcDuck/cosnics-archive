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
		$options = array();
		
		$languages = $adm->retrieve_languages();
		while ($language = $languages->next_result())
		{
			$options[$language->get_folder()] = $language->get_original_name();
		}
		
		return $options;
	}
	
	function get_themes()
	{
		$options = array();
		
		$path = Path :: get(SYS_LAYOUT_PATH);
		$directories = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);
		
		foreach($directories as $index => $directory)
		{
			if (substr($directory, 0 , 1) != '.')
			{
				$options[$directory] = DokeosUtilities :: underscores_to_camelcase($directory);
			}
		}
		
		return $options;
	}
}
?>
