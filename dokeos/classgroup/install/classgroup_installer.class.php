<?php
/**
 * @package classgroup.install
 */
require_once dirname(__FILE__).'/../lib/classgroupdatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
/**
 * This installer can be used to create the storage structure for the
 * classgroup application.
 */
class ClassGroupInstaller extends Installer
{
	private $values;
	/**
	 * Constructor
	 */
    function ClassGroupInstaller($values)
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
		
		$classgroupevents = array();
		$classgroupevents[] = Events :: create_event('create', 'classgroup');
		$classgroupevents[] = Events :: create_event('update', 'classgroup');
		$classgroupevents[] = Events :: create_event('delete', 'classgroup');
		$classgroupevents[] = Events :: create_event('empty', 'classgroup');
		$classgroupevents[] = Events :: create_event('subscribe_user', 'classgroup');
		$classgroupevents[] = Events :: create_event('unsubscribe_user', 'classgroup');
		
		
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
					if($tracker->get_class() == 'ClassGroupChangesTracker')
					{
						foreach($classgroupevents as $event)
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
	 * Parses an XML file and sends the request to the database manager
	 * @param String $path
	 */
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		$dm = ClassGroupDataManager :: get_instance();
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