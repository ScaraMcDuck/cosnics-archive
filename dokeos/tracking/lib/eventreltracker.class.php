<?php 
/**
 * tracking.lib
 */

/**
 * This class presents a event_rel_tracker
 *
 * @author Sven Vanpoucke
 */
class EventRelTracker
{
	/**
	 * EventRelTracker properties
	 */
	const PROPERTY_EVENTID = 'eventid';
	const PROPERTY_TRACKERID = 'trackerid';
	const PROPERTY_ACTIVE = 'active';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new EventRelTracker object
	 * @param array $defaultProperties The default properties
	 */
	function EventRelTracker($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_EVENTID, self :: PROPERTY_TRACKERID, self :: PROPERTY_ACTIVE);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Returns the eventid of this EventRelTracker.
	 * @return the eventid.
	 */
	function get_eventid()
	{
		return $this->get_default_property(self :: PROPERTY_EVENTID);
	}

	/**
	 * Sets the eventid of this EventRelTracker.
	 * @param eventid
	 */
	function set_eventid($eventid)
	{
		$this->set_default_property(self :: PROPERTY_EVENTID, $eventid);
	}

	/**
	 * Returns the trackerid of this EventRelTracker.
	 * @return the trackerid.
	 */
	function get_trackerid()
	{
		return $this->get_default_property(self :: PROPERTY_TRACKERID);
	}

	/**
	 * Sets the trackerid of this EventRelTracker.
	 * @param trackerid
	 */
	function set_trackerid($trackerid)
	{
		$this->set_default_property(self :: PROPERTY_TRACKERID, $trackerid);
	}

	/**
	 * Returns the active of this EventRelTracker.
	 * @return the active.
	 */
	function get_active()
	{
		return $this->get_default_property(self :: PROPERTY_ACTIVE);
	}

	/**
	 * Sets the active of this EventRelTracker.
	 * @param active
	 */
	function set_active($active)
	{
		$this->set_default_property(self :: PROPERTY_ACTIVE, $active);
	}

	/**
	 * Creates this event tracker relation in the database
	 */
	function create()
	{
		$trkdmg = TrackingDataManager :: get_instance();
		$this->set_id($trkdmg->get_next_id('event_rel_tracker'));
		$trkdmg->create_event_tracker_relation($this);
	}
	
	/**
	 * Updates this event tracker relation in the database
	 */
	function update()
	{
		$trkdmg = TrackingDataManager :: get_instance();
		$trkdmg->update_event_tracker_relation($this);
	}

}

?>