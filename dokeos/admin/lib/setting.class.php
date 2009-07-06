<?php
/**
 * @package admin.lib
 * @author Hans De Bisschop
 */

require_once dirname(__FILE__) . '/admin_data_manager.class.php';

class Setting
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_ID = 'id';
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_VARIABLE = 'variable';
    const PROPERTY_VALUE = 'value';
    
    private $id;
    private $defaultProperties;

    /**
     * Creates a new PM object.
     * @param int $id The numeric ID of the setting object. May be omitted
     *                if creating a new object.
     * @param array $defaultProperties The default properties of the setting
     *                                 object. Associative array.
     */
    function Setting($id = 0, $defaultProperties = array ())
    {
        $this->set_id($id);
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this setting object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this setting.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Get the default properties of all settings.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_APPLICATION, self :: PROPERTY_VARIABLE, self :: PROPERTY_VALUE);
    }

    /**
     * Sets a default property of this setting by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Checks if the given identifier is the name of a default setting
     * property.
     * @param string $name The identifier.
     * @return boolean True if the identifier is a property name, false
     *                 otherwise.
     */
    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    /**
     * Returns the id of this setting.
     * @return int The setting id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the application of this setting object
     * @return string The setting application
     */
    function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    /**
     * Returns the variable of this setting object
     * @return string the variable
     */
    function get_variable()
    {
        return $this->get_default_property(self :: PROPERTY_VARIABLE);
    }

    /**
     * Returns the value of this setting object
     * @return string the value
     */
    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Sets the id of this setting.
     * @param int $id The setting id.
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Sets the application of this setting.
     * @param string $application the setting application.
     */
    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    /**
     * Sets the variable of this setting.
     * @param string $variable the variable.
     */
    function set_variable($variable)
    {
        $this->set_default_property(self :: PROPERTY_VARIABLE, $variable);
    }

    /**
     * Sets the value of this setting.
     * @param string $value the value.
     */
    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    /**
     * Instructs the data manager to create the setting, making it
     * persistent. Also assigns a unique ID to the setting
     * @return boolean True if creation succeeded, false otherwise.
     */
    function create()
    {
        $adm = AdminDataManager :: get_instance();
        $id = $adm->get_next_setting_id();
        $this->set_id($id);
        return $adm->create_setting($this);
    }

    /**
     * Deletes this setting from persistent storage
     * @see PAdminDataManager::delete_setting()
     */
    function delete()
    {
        return AdminDataManager :: get_instance()->delete_setting($this);
    }

    /**
     * Updates this setting in persistent storage
     * @see AdminDataManager::update_setting()
     */
    function update()
    {
        return AdminDataManager :: get_instance()->update_setting($this);
    }

    static function get_table_name()
    {
        return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>
