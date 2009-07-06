<?php
/**
 * tracking.lib
 */

/**
 * This class presents a tracker_setting
 *
 * @author Sven Vanpoucke
 */
class TrackerSetting
{
    const CLASS_NAME = __CLASS__;

    /**
     * TrackerSetting properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_TRACKER_ID = 'tracker_id';
    const PROPERTY_SETTING = 'setting';
    const PROPERTY_VALUE = 'value';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new TrackerSetting object
     * @param array $defaultProperties The default properties
     */
    function TrackerSetting($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_TRACKER_ID, self :: PROPERTY_SETTING, self :: PROPERTY_VALUE);
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
     * Returns the id of this TrackerSetting.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this TrackerSetting.
     * @param id
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the tracker_id of this TrackerSetting.
     * @return the tracker_id.
     */
    function get_tracker_id()
    {
        return $this->get_default_property(self :: PROPERTY_TRACKER_ID);
    }

    /**
     * Sets the tracker_id of this TrackerSetting.
     * @param tracker_id
     */
    function set_tracker_id($tracker_id)
    {
        $this->set_default_property(self :: PROPERTY_TRACKER_ID, $tracker_id);
    }

    /**
     * Returns the setting of this TrackerSetting.
     * @return the setting.
     */
    function get_setting()
    {
        return $this->get_default_property(self :: PROPERTY_SETTING);
    }

    /**
     * Sets the setting of this TrackerSetting.
     * @param setting
     */
    function set_setting($setting)
    {
        $this->set_default_property(self :: PROPERTY_SETTING, $setting);
    }

    /**
     * Returns the value of this TrackerSetting.
     * @return the value.
     */
    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Sets the value of this TrackerSetting.
     * @param value
     */
    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    /**
     * Creates this event in the database
     */
    function create()
    {
        $trkdmg = TrackingDataManager :: get_instance();
        $this->set_id($trkdmg->get_next_id(self :: get_table_name()));
        $trkdmg->create_tracker_setting($this);
    }

    /**
     * Updates this event in the database
     */
    function update()
    {
        $trkdmg = TrackingDataManager :: get_instance();
        $trkdmg->update_tracker_setting($this);
    }

    static function get_table_name()
    {
        return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>