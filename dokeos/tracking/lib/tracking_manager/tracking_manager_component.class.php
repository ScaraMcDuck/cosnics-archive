<?php
/**
 * @package tracking.lib.trackingmanager
 */
/**
 * Base class for a tracking manager component.
 * A tracking manager provides different tools to the end tracker. Each tool is
 * represented by a tracking manager component and should extend this class.
 */

abstract class TrackingManagerComponent 
{
	/**
	 * The number of components already instantiated
	 */
	private static $component_count = 0;
	/**
	 * The tracking manager in which this component is used
	 */
	private $tracking_manager;
	/**
	 * The id of this component
	 */
	private $id;

	/**
	 * Constructor
	 * @param trackingManager $tracking_manager The tracking manager which
	 * provides this component
	 */
    function TrackingManagerComponent($tracking_manager) 
    {
    	$this->tracking_manager = $tracking_manager;
		$this->id =  ++self :: $component_count;
    }

    /**
	 * @see trackingManager::display_header()
	 */
	function display_header($breadcrumbs, $display_search = false)
	{
		$this->get_parent()->display_header($breadcrumbs, $display_search);
	}

	/**
	 * @see trackingManager::display_footer()
	 */
	function display_footer()
	{
		$this->get_parent()->display_footer();
	}

	/**
	 * @see trackingManager::display_message()
	 */
	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}

	/**
	 * @see trackingManager::display_error_message()
	 */
	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}

	/**
	 * @see trackingManager::display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}

	/**
	 * @see trackingManager::display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}

	/**
	 * @see trackingManager::display_popup_form()
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}

	/**
	 * Retrieve the tracking manager in which this component is active
	 * @return trackingManager
	 */
	function get_parent()
	{
		return $this->tracking_manager;
	}

	/**
	 * Retrieve the component id
	 */
	function get_component_id()
	{
		return $this->id;
	}

	/**
	 * @see trackingManager::get_parameters()
	 */
	function get_parameters($include_search = false)
	{
		return $this->get_parent()->get_parameters($include_search);
	}

	/**
	 * @see trackingManager::get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}

	/**
	 * @see trackingManager::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->get_parent()->set_parameter($name, $value);
	}

	/**
	 * @see trackingManager::force_menu_url()
	 */
	function force_menu_url($url)
	{
		return $this->get_parent()->force_menu_url($url);
	}

	/**
	 * @see trackingManager::get_url()
	 */
	function get_url($additional_parameters = array(), $include_search = false, $encode_entities = false)
	{
		return $this->get_parent()->get_url($additional_parameters, $include_search, $encode_entities);
	}

	/**
	 * @see trackingManager::get_link()
	 */
	function get_link($parameters = array (), $encode = false)
	{
		return $this->get_parent()->get_link($parameters, $encode);
	}

	/**
	 * @see trackingManager::redirect()
	 */
	function redirect($type = 'url', $message = null, $error_message = false, $extra_params = null)
	{
		return $this->get_parent()->redirect($type, $message, $error_message, $extra_params);
	}

	/**
	 * @see trackingManager::get_web_code_path()
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	
	/**
	 * Create a new tracking manager component
	 * @param string $type The type of the component to create.
	 * @param trackingManager $tracking_manager The tracking manager in
	 * which the created component will be used
	 */
	static function factory($type, $tracking_manager)
	{
		$filename = dirname(__FILE__) . '/component/'.DokeosUtilities :: camelcase_to_underscores($type) . '.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "' . $type . '" component');
		}
		$class = 'TrackingManager' . $type . 'Component';
		require_once $filename;
		return new $class($tracking_manager);
	}
	
	/**
	 * Retrieves the active user
	 * @return User the active user
	 * @see TrackingManager :: get_user()
	 */
	function get_user()
	{
		return $this->get_parent()->get_user();
	}

	/**
	 * Retrieves the browser url
	 * @return the browser url
	 * @see TrackingManager :: get_browser_url()
	 */
	function get_browser_url()
	{
		return $this->get_parent()->get_browser_url();
	}
	
	/**
	 * Retrieves the change active url
	 * @see TrackingManager :: get_change_active_url;
	 */
	function get_change_active_url($type, $event_id, $tracker_id = null)
	{
		return $this->get_parent()->get_change_active_url($type, $event_id, $tracker_id);
	}
	
	/** 
	 * Retrieves the event viewer url
	 * @see TrackingManager :: get_event_viewer_url()
	 */
	function get_event_viewer_url($event)
	{
		return $this->get_parent()->get_event_viewer_url($event);
	}
	
	/** 
	 * Retrieves the empty tracker url
	 * @see TrackingManager :: get_empty_tracker_url()
	 */
	function get_empty_tracker_url($type, $event_id, $tracker_id = null)
	{
		return $this->get_parent()->get_empty_tracker_url($type, $event_id,$tracker_id);
	}
	
	/**
	 * Retrieves the platform administration link
	 */
	function get_platform_administration_link()
	{
		return $this->get_parent()->get_platform_administration_link();
	}
	
	/**
	 * Retrieves the events
	 * @param Condition $condition
	 * @param int $offset
	 * @param int $count
	 * @param String $order_property
	 * @param String $order_direction
	 * @see TrackingManager :: retrieve_events();
	 */
	function retrieve_events($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_events($condition, $offset, $count, $order_property, $order_direction);
	}
	
	/**
	 * Count the events from a given condition
	 * @param Condition $conditions
	 */
	function count_events($conditions = null)
	{
		return $this->get_parent()->count_events($conditions);
	}
	
	/**
	 * Retrieves the trackers from a given event
	 * @see TrackingManager :: retrieve_trackers_from_event
	 */
	function retrieve_trackers_from_event($event_id)
	{
		return $this->get_parent()->retrieve_trackers_from_event($event_id);
	}
	
	/**
	 * Retrieves an event by the given id
	 * @param int $event_id
	 * @return Event event
	 */
	function retrieve_event($event_id)
	{
		return $this->get_parent()->retrieve_event($event_id);
	}
	
	/**
	 * Retrieves the event tracker relation by given id's
	 * @param int $event_id the event id
	 * @param int $tracker_id the tracker id
	 * @return EventTrackerRelation
	 * @see TrackingManager :: retrieve_event_tracker_relation
	 */
	function retrieve_event_tracker_relation($event_id, $tracker_id)
	{
		return $this->get_parent()->retrieve_event_tracker_relation($event_id, $tracker_id);
	}
	
	/**
	 * Retrieves the tracker for the given id
	 * @param int $tracker_id the given tracker id
	 * @return TrackerRegistration the tracker registration
	 */
	function retrieve_tracker_registration($tracker_id)
	{
		return $this->get_parent()->retrieve_tracker_registration($tracker_id);
	}
	
	/**
	 * Retrieves an event by name
	 * @param string $eventname 
	 * @return Event event
	 * @see TrackingManager :: retrieve_event_by_name
	 */
	function retrieve_event_by_name($eventname)
	{
		return $this->get_parent()->retrieve_event_by_name($eventname);
	}
	
}
?>