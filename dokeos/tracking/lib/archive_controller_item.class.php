<?php
/**
 * tracking.lib
 */

/**
 * This class presents a archive controller item
 *
 * @author Sven Vanpoucke
 */
class ArchiveControllerItem
{
    /**
     * Tracker properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_ORIGINAL_TABLE = 'original_table';
    const PROPERTY_START_DATE = 'start_date';
    const PROPERTY_END_DATE = 'end_date';
    const PROPERTY_TABLE_NAME = 'table_name';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;
    
    /**
     * Extra boolean to see if this archive controller item for a certain event is active
     * normally we could retrieve this in our relation but for use with simple table
     * we place it here
     */
    private $active;

    /**
     * Creates a new archive controller item object
     * @param array $defaultProperties The default properties
     */
    function ArchiveControllerItem($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_ORIGINAL_TABLE, self :: PROPERTY_START_DATE, self :: PROPERTY_END_DATE, self :: PROPERTY_TABLE_NAME);
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
     * Sets the default properties of this original_table
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this archive controller item.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this archive controller item.
     * @param id
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the original_table of this archive controller item.
     * @return the original_table.
     */
    function get_original_table()
    {
        return $this->get_default_property(self :: PROPERTY_ORIGINAL_TABLE);
    }

    /**
     * Sets the original_table of this archive controller item.
     * @param original_table
     */
    function set_original_table($original_table)
    {
        $this->set_default_property(self :: PROPERTY_ORIGINAL_TABLE, $original_table);
    }

    /**
     * Returns the start_date of this archive controller item.
     * @return the start_date.
     */
    function get_start_date()
    {
        return $this->get_default_property(self :: PROPERTY_START_DATE);
    }

    /**
     * Sets the start_date of this archive controller item.
     * @param start_date
     */
    function set_start_date($start_date)
    {
        $this->set_default_property(self :: PROPERTY_START_DATE, $this->to_db_date($start_date));
    }

    /**
     * Returns the end_date of this Tracker.
     * @return the end_date.
     */
    function get_end_date()
    {
        return $this->get_default_property(self :: PROPERTY_END_DATE);
    }

    /**
     * Sets the end_date of this Tracker.
     * @param end_date
     */
    function set_end_date($end_date)
    {
        $this->set_default_property(self :: PROPERTY_END_DATE, $this->to_db_date($end_date));
    }

    /**
     * Returns the table_name of this Tracker.
     * @return the table_name.
     */
    function get_table_name()
    {
        return $this->get_default_property(self :: PROPERTY_TABLE_NAME);
    }

    /**
     * Sets the table_name of this Tracker.
     * @param table_name
     */
    function set_table_name($table_name)
    {
        $this->set_default_property(self :: PROPERTY_TABLE_NAME, $table_name);
    }

    /**
     * Creates this archive controller item in the database
     */
    function create()
    {
        $trkdmg = TrackingDataManager :: get_instance();
        $this->set_id($trkdmg->get_next_id(self :: get_table_name()));
        return $trkdmg->create_archive_controller_item($this);
    }

    /**
     * Auxillary function to convert to unix timestamp to db date
     * @param int $date unix timestamp
     */
    function to_db_date($date)
    {
        $trkdmg = TrackingDataManager :: get_instance();
        return $trkdmg->to_db_date($date);
    }

}

?>