<?php
/**
 * @package repository
 */
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once dirname(__FILE__) . '/tracker.class.php';
require_once dirname(__FILE__) . '/event.class.php';
require_once dirname(__FILE__) . '/trackersetting.class.php';

/**
 *	This is a skeleton for a data manager for tracking manager
 *	Data managers must extend this class and implement its abstract methods.
 *	If the user configuration dictates that the "database" data manager is to
 *	be used, this class will automatically attempt to instantiate
 *	"DatabaseTrackingDataManager"; hence, this naming convention must be
 *	respected for all extensions of this class.
 *
 *	@author Sven Vanpoucke
 */
abstract class TrackingDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;
	
	/**
	 * Constructor.
	 */
	protected function TrackingDataManager()
	{
		$this->initialize();
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return TrackingDataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'TrackingDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}

	/**
	 * Creates a storage unit
	 * @param string $name Name of the storage unit
	 * @param array $properties Properties of the storage unit
	 * @param array $indexes The indexes which should be defined in the created
	 * storage unit
	 */
	abstract function create_storage_unit($name,$properties,$indexes);
	/**
	 * Creates an event in the database
	 * @param Event $event
	 */
	abstract function create_event($event);
	/**
	 * Registers a tracker in the database
	 * @param Tracker $tracker
	 */
	abstract function register_tracker($tracker);
	/**
	 * Registers a tracker to an event
	 * @param EventTrackerRelation $eventtrackerrelation
	 */
	abstract function create_event_tracker_relation($eventtrackerrelation);
	/**
	 * Updates an event (needed for change of activity)
	 * @param Event $event
	 */
	abstract function update_event($event);
	/**
	 * Updates an event tracker relation (needed for change of activity)
	 * @param EventTrackerRelation $eventtrackerrelation
	 */
	abstract function update_event_tracker_relation($eventtrackerrelation);
	/**
	 * Retrieves the event with the given name
	 * @param String $name
	 */
	abstract function retrieve_event_by_name($eventname);
	/**
	 * Retrieve all trackers from an event
	 * @param Event $event
	 * @param Bool $active true if only the active ones should be shown (default true)
	 */
	abstract function retrieve_trackers_from_event($event, $active = true);
	/**
	 * Retrieves all events 
	 */
	abstract function retrieve_events();
}
?>