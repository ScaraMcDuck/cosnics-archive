<?php
/**
 * Class representing a reporting template
 *
 * @author: Michael Kyndt
 */

require_once Path :: get_common_path() . 'data_class.class.php';

class ReportingTemplateRegistration extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_TITLE = 'title';
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_CLASSNAME = 'class';
    const PROPERTY_PLATFORM = 'platform';
    const PROPERTY_DESCRIPTION = 'description';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TITLE, self :: PROPERTY_APPLICATION, self :: PROPERTY_CLASSNAME, self :: PROPERTY_PLATFORM, self :: PROPERTY_DESCRIPTION));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return ReportingDataManager :: get_instance();
    }

    /**
     * Checks if the reporting template registration is aplatform template.
     * @return int
     */
    function isPlatformTemplate()
    {
        return $this->get_default_property(self :: PROPERTY_PLATFORM) == '1';
    }

    /*
 	 * Getters and setters
 	 */

    public function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    public function set_title($value)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $value);
    }

    public function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    public function set_application($value)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $value);
    }

    public function get_classname()
    {
        return $this->get_default_property(self :: PROPERTY_CLASSNAME);
    }

    public function set_classname($value)
    {
        $this->set_default_property(self :: PROPERTY_CLASSNAME, $value);
    }

    public function get_platform()
    {
        return $this->get_default_property(self :: PROPERTY_PLATFORM);
    }

    public function set_platform($value)
    {
        $this->set_default_property(self :: PROPERTY_PLATFORM, $value);
    }

    public function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    public function set_description($value)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $value);
    }

    static function get_table_name()
    {
        return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
} //class ReportingTemplateRegistration
?>
