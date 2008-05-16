<?php
/**
 * $Id$
 * @package repository
 * @todo Some more common install-functions can be added here. Example: A
 * function which returns the list of xml-files from a given directory.
 */

require_once Path :: get_tracking_path() .'lib/trackerregistration.class.php';
require_once Path :: get_tracking_path() .'lib/eventreltracker.class.php';
 
class Installer
{
	/**
	 * The datamanager which can be used by the installer of the application
	 */
	private $datamanager;
	
	/**
	 * Message to be displayed upon completion of the installation procedure
	 */
	private $message;
	
	/**
	 * Form values passed on from the installation wizard
	 */
	private $form_values;
	/**
	 * Constructor
	 */
    function Installer($form_values, $datamanager = null)
    {
    	$this->form_values = $form_values;
    	$this->datamanager = $datamanager;
    	$this->message = array();
    }
    /**
     * Parses an XML file describing a storage unit.
     * For defining the 'type' of the field, the same definition is used as the
     * PEAR::MDB2 package. See http://pear.php.net/manual/en/package.database.
     * mdb2.datatypes.php
     * @param string $file The complete path to the XML-file from which the
     * storage unit definition should be read.
     * @return array An with values for the keys 'name','properties' and
     * 'indexes'
     */
    public static function parse_xml_file($file)
    { 
		$doc = new DOMDocument();
		$doc->load($file);
		$object = $doc->getElementsByTagname('object')->item(0);
		$name = $object->getAttribute('name');
		$xml_properties = $doc->getElementsByTagname('property');
		$attributes = array('type','length','unsigned','notnull','default','autoincrement','fixed');
		foreach($xml_properties as $index => $property)
		{
			 $property_info = array();
			 foreach($attributes as $index => $attribute)
			 {
			 	if($property->hasAttribute($attribute))
			 	{
			 		$property_info[$attribute] = $property->getAttribute($attribute);
			 	}
			 }
			 $properties[$property->getAttribute('name')] = $property_info;
		}
		$xml_indexes = $doc->getElementsByTagname('index');
		foreach($xml_indexes as $key => $index)
		{
			 $index_info = array();
			 $index_info['type'] = $index->getAttribute('type');
			 $index_properties = $index->getElementsByTagname('indexproperty');
			 foreach($index_properties as $subkey => $index_property)
			 {
			 	$index_info['fields'][$index_property->getAttribute('name')] = array('length' => $index_property->getAttribute('length'));
			 }
			 $indexes[$index->getAttribute('name')] = $index_info;
		}
		$result = array();
		$result['name'] = $name;
		$result['properties'] = $properties;
		$result['indexes'] = $indexes;
		
		return $result;
    }
    
    function add_message($message)
    {
    	$this->message[] = $message;
    }
    
    function set_message($message)
    {
    	$this->message = $message;
    }
    
    function get_message()
    {
    	return $this->message;
    }
    
    function set_form_values($form_values)
    {
    	$this->form_values = $form_values;
    }
    
    function get_form_values()
    {
    	return $this->form_values;
    }
    
    function set_datamanager($datamanager)
    {
    	$this->datamanager = $datamanager;
    }
    
    function get_datamanager()
    {
    	return $this->datamanager;
    }
    
    function retrieve_message()
    {
    	return implode('<br />'."\n", $this->get_message());
    }
    
	/**
	 * Parses an XML file and sends the request to the database manager
	 * @param String $path
	 */
	function create_storage_unit($path)
	{
		$storage_unit_info = self :: parse_xml_file($path);
		$this->add_message(Translation :: get('StorageUnitCreation') . ': <em>'.$storage_unit_info['name'] . '</em>');
		if (!$this->datamanager->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']))
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
	
	function parse_application_events($file)
	{
		$doc = new DOMDocument();
		$result = array();
		
		$doc->load($file);
		$object = $doc->getElementsByTagname('application')->item(0);
		$result['name'] = $object->getAttribute('name');
		
		// Get events
		$events = $doc->getElementsByTagname('event');
		$trackers = array();
		
		foreach($events as $index => $event)
		{
			$event_name = $event->getAttribute('name');
			$trackers = array();
			
			// Get trackers in event
			$event_trackers = $event->getElementsByTagname('tracker');
			$attributes = array('name', 'active');
			
			foreach($event_trackers as $index => $event_tracker)
			{
				$property_info = array();
				
				foreach($attributes as $index => $attribute)
				{
					if($event_tracker->hasAttribute($attribute))
				 	{
				 		$property_info[$attribute] = $event_tracker->getAttribute($attribute);
				 	}
				}
				$trackers[$event_tracker->getAttribute('name')] = $property_info;
			}
			
			$result['events'][$event_name]['name'] = $event_name;
			$result['events'][$event_name]['trackers'] = $trackers;
		}
		
		return $result;
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
		if (!$tracker->create())
		{
			return false;
		}
		
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
		if ($rel->create())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Registers the trackers, events and creates the storage units for the trackers
	 */
	function register_trackers($tracker_event_matrix = array())
	{
		$application_class = str_replace('Installer', '', get_class($this));
		$application = RepositoryUtilities :: camelcase_to_underscores($application_class);
		
		$base_path = (Application :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
		
		$dir = $base_path . $application . '/trackers/tracker_tables/';
		$files = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
		$this->set_datamanager(TrackingDataManager :: get_instance());
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'xml'))
			{
				$this->create_storage_unit($file);
			}
		}
		
		$path = '/' . $application . '/trackers/';
		
		$trackers_file = $base_path . $application . '/trackers/trackers_users.xml';
		
		if (file_exists($trackers_file))
		{
			$xml = $this->parse_application_events($trackers_file);
			
			$registered_trackers = array();
			
			foreach($xml['events'] as $event_name => $event_properties)
			{
				$the_event = Events :: create_event($event_properties['name'], $xml['name']);
				if (!$the_event)
				{
					$error_message = '<span style="color: red; font-weight: bold;">' . Translation :: get('EventCreationFailed') . ': <em>'.$event_properties['name'] . '</em></span>';
					$this->add_message($error_message);
					$this->add_message(Translation :: get('ApplicationInstallFailed'));
					$this->add_message(Translation :: get('PlatformInstallFailed'));
					return false;
				}
				else
				{
					$this->add_message(Translation :: get('EventCreated') . ': ' . $event_properties['name']);
				}
				
				foreach ($event_properties['trackers'] as $tracker_name => $tracker_properties)
				{
					if (!array_key_exists($tracker_properties['name'], $registered_trackers))
					{
						$the_tracker = $this->register_tracker($path, $tracker_properties['name'] . '_tracker');
						if (!$the_tracker)
						{
							$error_message = '<span style="color: red; font-weight: bold;">' . Translation :: get('TrackerRegistrationFailed') . ': <em>'.$event_properties['name'] . '</em></span>';
							$this->add_message($error_message);
							$this->add_message(Translation :: get('ApplicationInstallFailed'));
							$this->add_message(Translation :: get('PlatformInstallFailed'));
							return false;
						}
						else
						{
							$this->add_message(Translation :: get('TrackersRegistered') . ': ' . $tracker_properties['name']);
						}
						$registered_trackers[$tracker_properties['name']] = $the_tracker;
					}
					
					$success = $this->register_tracker_to_event($registered_trackers[$tracker_properties['name']], $the_event);
					if ($success)
					{
						$this->add_message(Translation :: get('TrackersRegisteredToEvent') . ': ' . $event_properties['name'] . ' + ' . $tracker_properties['name']);
					}				
					else
					{
						$error_message = '<span style="color: red; font-weight: bold;">' . Translation :: get('TrackerRegistrationToEventFailed') . ': <em>'.$event_properties['name'] . '</em></span>';
						$this->add_message($error_message);
						$this->add_message(Translation :: get('ApplicationInstallFailed'));
						$this->add_message(Translation :: get('PlatformInstallFailed'));
						return false;
					}
				}
			}
		}
		elseif (count($files) > 0)
		{
			$warning_message = '<span style="color: orange; font-weight: bold;">' . Translation :: get('UnlinkedTrackers') . ': <em>'. Translation :: get('Check') . ' ' . $path . '</em></span>';
			$this->add_message($warning_message);
		}
		
		return true;
	}
	
	/**
	 * Creates an application-specific installer.
	 * @param string $application The application for which we want to start the installer.
	 * @param string $values The form values passed on by the wizard.
	 */
	static function factory($application, $values)
	{
		$class = Application :: application_to_class($application) . 'Installer';
		$base_path = (Application :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
		
		require_once($base_path . $application . '/install/'. $application .'_installer.class.php');
		return new $class ($values);
	}
}
?>