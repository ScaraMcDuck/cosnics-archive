<?php
/**
 * $Id: repositorydatamanager.class.php 9176 2006-08-30 09:08:17Z bmol $
 * @package repository
 */
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';
/**
 * This	 installer can be used to create the storage structure for the
 * repository.
 */
class RepositoryInstaller extends Installer
{
	/**
	 * Constructor
	 */
	function RepositoryInstaller()
	{
	}
	/**
	 * Runs the install-script. After creating the necessary tables to store the
	 * common learning object information, this function will scan the
	 * directories of all learning object types. When an XML-file describing a
	 * storage unit is found, this function will parse the file and create the
	 * storage unit.
	 */
	function install()
	{
		$dir_lo		= dirname(__FILE__) . '/../lib/learning_object';
		$dir_app	= dirname(__FILE__);
		$files_lo	= FileSystem :: get_directory_content($dir_lo, FileSystem :: LIST_FILES);
		$files_app	= FileSystem :: get_directory_content($dir_app, FileSystem :: LIST_FILES);
		
		$files = array_merge($files_app, $files_lo);
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'xml'))
			{
				if (!$this->create_storage_unit($file))
				{
					return array('success' => false, 'message' => $this->retrieve_message());
				}
			}
		}
		
		if(!$this->register_trackers())
		{
			return array('success' => false, 'message' => $this->retrieve_message());
		}
		
		$success_message = '<span style="color: green; font-weight: bold;">' . Translation :: get('ApplicationInstallSuccess') . '</span>';
		$this->add_message($success_message);
		return array('success' => true, 'message' => $this->retrieve_message());
	}

	
	/**
	 * Registers the trackers, events and creates the storage units for the trackers
	 */
	function register_trackers()
	{
		$dir = dirname(__FILE__) . '/../trackers/tracker_tables';
		$files = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
		$trkinstaller = new TrackingInstaller();
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'xml'))
			{
				if (!$trkinstaller->create_storage_unit($file))
				{
					return false;
				}
			}
		}
		
		$repository_events = array();
		$repository_events[] = Events :: create_event('create', 'repository');
		$repository_events[] = Events :: create_event('update', 'repository');
		$repository_events[] = Events :: create_event('delete', 'repository');
		$repository_events[] = Events :: create_event('move', 'repository');
		$repository_events[] = Events :: create_event('metadata', 'repository');
		$repository_events[] = Events :: create_event('publication_delete', 'repository');
		$repository_events[] = Events :: create_event('publication_update', 'repository');
		$repository_events[] = Events :: create_event('version', 'repository');
		$repository_events[] = Events :: create_event('restore', 'repository');
		$repository_events[] = Events :: create_event('reverte', 'repository');
		
		$path = '/classgroup/trackers/';
		
		$dir = dirname(__FILE__) . '/../trackers/';
		$files = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'php'))
			{
				$filename = basename($file);
				$filename = substr($filename, 0, strlen($filename) - strlen('.class.php'));
				
				$tracker = $trkinstaller->register_tracker($path, $filename);
				if (!$tracker)
				{
					return false;
				}
				else
				{
					if($tracker->get_class() == 'RepositoryChangesTracker')
					{
						foreach($repository_events as $event)
						{
							if(!$trkinstaller->register_tracker_to_event($tracker, $event)) return false;
						}
						
						$this->add_message(Translation :: get('TrackersRegistered') . ': ' . $filename);
						continue;
					}
					else
						echo($tracker->get_class());
				}
				
				
			}
		}
		
		return true;
	}

	/**
	 * Parses an XML-file in which a storage unit is described. After parsing,
	 * the create_storage_unit function of the RepositoryDataManager is used to
	 * create the actual storage unit depending on the implementation of the
	 * datamanager.
	 * @param string $path The path to the XML-file to parse
	 */
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		$dm = RepositoryDataManager :: get_instance();
		$this->add_message(Translation :: get('StorageUnitCreation') . ': <em>'.$storage_unit_info['name'] . '</em>');
		if (!$dm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']))
		{
			$error_message = '<span style="color: red; font-weight: bold;">' . Translation :: get('StorageUnitCreationFailed') . ': <em>'.$storage_unit_info['name'] . '</em></span>';
			$this->add_message($error_message);
			$this->add_message(Translation :: get('ApplicationInstallFailed'));
			$this->add_message(Translation :: get('PlatformInstallFailed'));
			
			return false;
		}
		else
		{
			return true;
		}

	}
}
?>