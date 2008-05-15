<?php
/**
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../lib/rightsdatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * weblcms application.
 */
class RightsInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function RightsInstaller()
    {
    	parent :: __construct(RightsDataManager :: get_instance());
    }
	/**
	 * Runs the install-script.
	 * @todo This function now uses the function of the RepositoryInstaller
	 * class. These shared functions should be available in a common base class.
	 */
	function install()
	{
		$dir = dirname(__FILE__);
		$files = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
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
		
		$rolesrightschangesevents = array();
		$rolesrightschangesevents[] = Events :: create_event('role_create', 'roles_rights');
		$rolesrightschangesevents[] = Events :: create_event('right_create', 'roles_rights');
		$rolesrightschangesevents[] = Events :: create_event('location_create', 'roles_rights');
		$rolesrightschangesevents[] = Events :: create_event('role_right_location_create', 'roles_rights');
		$rolesrightschangesevents[] = Events :: create_event('role_update', 'roles_rights');
		$rolesrightschangesevents[] = Events :: create_event('right_update', 'roles_rights');
		$rolesrightschangesevents[] = Events :: create_event('location_update', 'roles_rights');
		$rolesrightschangesevents[] = Events :: create_event('role_right_location_update', 'roles_rights');
		$rolesrightschangesevents[] = Events :: create_event('role_delete', 'roles_rights');
		$rolesrightschangesevents[] = Events :: create_event('right_delete', 'roles_rights');
		$rolesrightschangesevents[] = Events :: create_event('location_delete', 'roles_rights');
		$rolesrightschangesevents[] = Events :: create_event('role_right_location_delete', 'roles_rights');
		
		
		$path = '/rights/trackers/';
		
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
					if($tracker->get_class() == 'RolesRightChangesTracker')
					{
						foreach($rolesrightschangesevents as $event)
						{
							if(!$trkinstaller->register_tracker_to_event($tracker, $event)) return false;
						}
						
						$this->add_message(Translation :: get('TrackersRegistered') . ': ' . $filename);
						continue;
					}
				}
				
				
			}
		}
		
		return true;
	}
}
?>