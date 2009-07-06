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
    const CLASS_NAME = __CLASS__;
    
    /**
     * EventRelTracker properties
     */
    const PROPERTY_EVENT_ID = 'event_id';
    const PROPERTY_TRACKER_ID = 'tracker_id';
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
        return array(self :: PROPERTY_EVENT_ID, self :: PROPERTY_TRACKER_ID, self :: PROPERTY_ACTIVE);
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
     * Returns the event_id of this EventRelTracker.
     * @return the event_id.
     */
    function get_event_id()
    {
        return $this->get_default_property(self :: PROPERTY_EVENT_ID);
    }

    /**
     * Sets the event_id of this EventRelTracker.
     * @param event_id
     */
    function set_event_id($event_id)
    {
        $this->set_default_property(self :: PROPERTY_EVENT_ID, $event_id);
    }

    /**
     * Returns the tracker_id of this EventRelTracker.
     * @return the tracker_id.
     */
    function get_tracker_id()
    {
        return $this->get_default_property(self :: PROPERTY_TRACKER_ID);
    }

    /**
     * Sets the tracker_id of this EventRelTracker.
     * @param tracker_id
     */
    function set_tracker_id($tracker_id)
    {
        $this->set_default_property(self :: PROPERTY_TRACKER_ID, $tracker_id);
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
        return $trkdmg->create_event_tracker_relation($this);
    }

    /**
     * Updates this event tracker relation in the database
     */
    function update()
    {
        $trkdmg = TrackingDataManager :: get_instance();
        return $trkdmg->update_event_tracker_relation($this);
    }

    static function get_table_name()
    {
        return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>