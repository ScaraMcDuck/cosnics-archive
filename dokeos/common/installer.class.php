<?php
/**
 * $Id$
 * @package repository
 * @todo Some more common install-functions can be added here. Example: A
 * function which returns the list of xml-files from a given directory.
 */

require_once Path :: get_tracking_path() .'lib/tracking_data_manager.class.php';
require_once Path :: get_tracking_path() .'lib/tracker_registration.class.php';
require_once Path :: get_tracking_path() .'lib/event_rel_tracker.class.php';
require_once Path :: get_admin_path() .'lib/admin_data_manager.class.php';
require_once Path :: get_admin_path() .'lib/setting.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
 
abstract class Installer
{
	const TYPE_NORMAL = '1';
	const TYPE_CONFIRM = '2';
	const TYPE_WARNING = '3';
	const TYPE_ERROR = '4';
	
	/**
	 * The datamanager which can be used by the installer of the application
	 */
	private $data_manager;
	
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
    function Installer($form_values, $data_manager = null)
    {
    	$this->form_values = $form_values;
    	$this->data_manager = $data_manager;
    	$this->message = array();
    }
    
    function install()
    {
		$dir = $this->get_path();
		$files = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'xml'))
			{
				if (!$this->create_storage_unit($file))
				{
					return false;
				}
			}
		}
		
		if (!$this->configure_application())
		{
			return false;
		}
		
		if (method_exists($this, 'install_extra'))
		{
			if (!$this->install_extra())
			{
				return false;
			}
		}
		
		if(!$this->register_trackers())
		{
			return false;
		}
		
		return $this->installation_successful();
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
    
    function add_message($type = self :: TYPE_NORMAL, $message)
    {
    	switch ($type)
    	{
    		case self :: TYPE_NORMAL :
    			$this->message[] = $message;
    			break;
    		case self :: TYPE_CONFIRM :
    			$this->message[] = '<span style="color: green; font-weight: bold;">' . $message . '</span>';
    			break;
    		case self :: TYPE_WARNING :
    			$this->message[] = '<span style="color: orange; font-weight: bold;">' . $message . '</span>';
    			break;
    		case self :: TYPE_ERROR :
    			$this->message[] = '<span style="color: red; font-weight: bold;">' . $message . '</span>';
    			break;
    		default :
    			$this->message[] = $message;
    			break;
    	}
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
    
    function set_data_manager($data_manager)
    {
    	$this->data_manager = $data_manager;
    }
    
    function get_data_manager()
    {
    	return $this->data_manager;
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
		$this->add_message(self :: TYPE_NORMAL, Translation :: get('StorageUnitCreation') . ': <em>'.$storage_unit_info['name'] . '</em>');
		if (!$this->data_manager->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']))
		{
			return $this->installation_failed(Translation :: get('StorageUnitCreationFailed') . ': <em>'.$storage_unit_info['name'] . '</em>');
		}
		else
		{
			return true;
		}
	}
	
	// TODO: It's probably a good idea to write some kind of XML-parsing class that automatically converts the entire thing to a uniform array or object.
	
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
	
	function parse_application_settings($file)
	{
		$doc = new DOMDocument();
		
		$doc->load($file);
		$object = $doc->getElementsByTagname('application')->item(0);
		
		// Get events
		$events = $doc->getElementsByTagname('setting');
		$settings = array();
		
		foreach($events as $index => $event)
		{
			$settings[$event->getAttribute('name')] = $event->getAttribute('default');
		}
		
		return $settings;
	}
	
	/**
	 * Function used to register a tracker
	 */
	function register_tracker($path, $class)
	{	
		$tracker = new TrackerRegistration();
		$class = DokeosUtilities :: underscores_to_camelcase($class);
		$tracker->set_class($class);
		$tracker->set_path($path);
		if (!$tracker->create())
		{
			return false;
		}
		
		return $tracker;
	}
	
	/**
	 * Function used to register a tracker to an event
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
	function register_trackers()
	{
		$application_class = str_replace('Installer', '', get_class($this));
		$application = DokeosUtilities :: camelcase_to_underscores($application_class);
		
		$base_path = (Application :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
		
		$dir = $base_path . $application . '/trackers/tracker_tables/';
		
		if (is_dir($dir))
		{
			$files = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
			
			$this->set_data_manager(TrackingDataManager :: get_instance());
			
			foreach($files as $file)
			{
				if ((substr($file, -3) == 'xml'))
				{
					$this->create_storage_unit($file);
				}
			}
		}
		
		$path = '/' . $application . '/trackers/';
		
		$trackers_file = $base_path . $application . '/trackers/trackers_'. $application .'.xml';
		
		if (file_exists($trackers_file))
		{
			$xml = $this->parse_application_events($trackers_file);
			
			if (isset($xml['events']))
			{
				$registered_trackers = array();
				
				foreach($xml['events'] as $event_name => $event_properties)
				{
					$the_event = Events :: create_event($event_properties['name'], $xml['name']);
					if (!$the_event)
					{
						$this->installation_failed(Translation :: get('EventCreationFailed') . ': <em>'.$event_properties['name'] . '</em>');
					}
					
					foreach ($event_properties['trackers'] as $tracker_name => $tracker_properties)
					{
						if (!array_key_exists($tracker_properties['name'], $registered_trackers))
						{
							$the_tracker = $this->register_tracker($path, $tracker_properties['name'] . '_tracker');
							if (!$the_tracker)
							{
								$this->installation_failed(Translation :: get('TrackerRegistrationFailed') . ': <em>'.$tracker_properties['name'] . '</em>');
							}
							$registered_trackers[$tracker_properties['name']] = $the_tracker;
						}
						
						$success = $this->register_tracker_to_event($registered_trackers[$tracker_properties['name']], $the_event);
						if ($success)
						{
							$this->add_message(self :: TYPE_NORMAL, Translation :: get('TrackersRegisteredToEvent') . ': ' . $event_properties['name'] . ' + ' . $tracker_properties['name']);
						}				
						else
						{
							$this->installation_failed(Translation :: get('TrackerRegistrationToEventFailed') . ': <em>'.$event_properties['name'] . '</em>');
						}
					}
				}
			}
			elseif (count($files) > 0)
			{
				$warning_message = Translation :: get('UnlinkedTrackers') . ': <em>'. Translation :: get('Check') . ' ' . $path . '</em>';
				$this->add_message(self :: TYPE_WARNING, $warning_message);
			}
		}
		elseif (count($files) > 0)
		{
			$warning_message = Translation :: get('UnlinkedTrackers') . ': <em>'. Translation :: get('Check') . ' ' . $path . '</em>';
			$this->add_message(self :: TYPE_WARNING, $warning_message);
		}
		
		return true;
	}
	
	function configure_application()
	{
		$application_class = str_replace('Installer', '', get_class($this));
		$application = DokeosUtilities :: camelcase_to_underscores($application_class);
		
		$base_path = (Application :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
		
		$settings_file = $base_path . $application . '/settings/settings_'. $application .'.xml';
		
		if (file_exists($settings_file))
		{
			$xml = $this->parse_application_settings($settings_file);
			
			foreach($xml as $name => $value)
			{
				$setting = new Setting();
				$setting->set_application($application);
				$setting->set_variable($name);
				$setting->set_value($value);
				
				if (!$setting->create())
				{
					$message = Translation :: get('ApplicationConfigurationFailed');
					$this->installation_failed($message);
				}
			}
		}
		
		return true;
	}
	
	function installation_failed($error_message)
	{
		$this->add_message(self :: TYPE_ERROR, $error_message);
		$this->add_message(self :: TYPE_ERROR, Translation :: get('ApplicationInstallFailed'));
		$this->add_message(self :: TYPE_ERROR, Translation :: get('PlatformInstallFailed'));
		return false;
	}
	
	function installation_successful()
	{
		$this->add_message(self :: TYPE_CONFIRM, Translation :: get('ApplicationInstallSuccess'));
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
	
	abstract function get_path();
}
?>