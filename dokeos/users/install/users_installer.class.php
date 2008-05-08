<?php
/**
 * $Id: personal_calendar_installer.class.php 12686 2007-07-03 11:32:57Z bmol $
 * @package users.install
 */
require_once dirname(__FILE__).'/../lib/usersdatamanager.class.php';
require_once dirname(__FILE__).'/../lib/user.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
/**
 * This installer can be used to create the storage structure for the
 * users application.
 */
class UsersInstaller extends Installer
{
	private $values;
	/**
	 * Constructor
	 */
    function UsersInstaller($values)
    {
    	$this->values = $values;
    }
	/**
	 * Runs the install-script.
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
		
		if (!$this->create_admin_account())
		{
			return array('success' => false, 'message' => $this->retrieve_message());
		}
		else
		{
			$this->add_message(Translation :: get('AdminAccountCreated'));
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
		
		$loginevent = Events :: create_event('login', 'users');
		$logoutevent = Events :: create_event('logout', 'users');
		
		$userchangesevents = array();
		$userchangesevents[] = Events :: create_event('create', 'users');
		$userchangesevents[] = Events :: create_event('update', 'users');
		$userchangesevents[] = Events :: create_event('delete', 'users');
		$userchangesevents[] = Events :: create_event('register', 'users');
		$userchangesevents[] = Events :: create_event('import', 'users');
		$userchangesevents[] = Events :: create_event('export', 'users');
		$userchangesevents[] = Events :: create_event('reset_password', 'users');
		$userchangesevents[] = Events :: create_event('quota', 'users');
		
		$path = '/users/trackers/';
		
		$dir = dirname(__FILE__) . '/../trackers/';
		$files = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'php'))
			{
				$filename = basename($file);
				$filename = substr($filename, 0, strlen($filename) - strlen('.class.php'));
				
				if($filename == 'usertracker') continue;
				
				$tracker = $trkinstaller->register_tracker($path, $filename);
				if (!$tracker)
				{
					return false;
				}
				else
				{
					if($tracker->get_class() == 'LoginLogoutTracker')
					{
						if(!$trkinstaller->register_tracker_to_event($tracker, $logoutevent)) return false;
						continue;
					}
					
					if($tracker->get_class() == 'UserChangesTracker')
					{
						foreach($userchangesevents as $event)
						{
							if(!$trkinstaller->register_tracker_to_event($tracker, $event)) return false;
						}
						continue;
					}
					
					if(!$trkinstaller->register_tracker_to_event($tracker, $loginevent)) return false;
				}
				
				$this->add_message(Translation :: get('TrackersRegistered') . ': ' . $filename);
			}
		}
		
		return true;
	}

	/**
	 * Parses an XML file and sends the request to the database manager
	 * @param String $path
	 */
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		$dm = UsersDataManager :: get_instance();
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
	
	function create_admin_account()
	{
		$values = $this->values;
		
		$user = new User();
		
		$user->set_lastname($values['admin_surname']);
		$user->set_firstname($values['admin_firstname']);
		$user->set_username($values['admin_username']);
		$user->set_password(md5($values['admin_password']));
		$user->set_auth_source('platform');
		$user->set_email($values['admin_email']);
		$user->set_status('1');
		$user->set_platformadmin('1');
		$user->set_official_code('ADMIN');
		$user->set_phone($values['admin_phone']);
		$user->set_language($values['install_language']);
		$user->set_disk_quota('209715200');
		$user->set_database_quota('300');
		$user->set_version_quota('20');
		
		if (!$user->create())
		{
			return false;
		}
		else
		{
			return true;
		}
		
	}
}
?>