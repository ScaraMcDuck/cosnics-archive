<?php
/**
 * @package contentboxes.install
 */
require_once dirname(__FILE__).'/../lib/trackingdatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_tracking_path() .'lib/trackerregistration.class.php';
require_once Path :: get_tracking_path() .'lib/eventreltracker.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
/**
 * This	 installer can be used to create the contentboxes structure
 */
class TrackingInstaller extends Installer
{
	/**
	 * Constructor
	 */
	function TrackingInstaller($values)
	{
		parent :: __construct($values, TrackingDataManager :: get_instance());
	}
	
	/**
	 * Runs the install-script. Creates the necessary tables for contentbox storage
	 */
	function install()
	{
		$dir	= dirname(__FILE__);
		$files	= FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
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
		
		$success_message = '<span style="color: green; font-weight: bold;">' . Translation :: get('ApplicationInstallSuccess') . '</span>';
		$this->add_message($success_message);
		return array('success' => true, 'message' => $this->retrieve_message());
	}
	
	/**
	 * Function used by other installers to register a tracker
	 */
	function register_tracker($path, $class)
	{	
		$tracker = new TrackerRegistration();
		
		$class = RepositoryUtilities :: underscores_to_camelcase($class);
		
		$tracker->set_class($class);
		$tracker->set_path($path);
		
		$tracker->create();
		
		return $tracker;
	}
	
	/**
	 * Function used by other installers to register a tracker to an event
	 */
	function register_tracker_to_event($tracker, $event)
	{
		$rel = new EventRelTracker();
		$rel->set_tracker_id($tracker->get_id());
		$rel->set_event_id($event->get_id());
		$rel->set_active(true);
		$rel->create();
		
		return $rel;
	}
}
?>