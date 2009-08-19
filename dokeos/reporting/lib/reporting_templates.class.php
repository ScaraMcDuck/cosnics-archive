<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/reporting_template_registration.class.php';
require_once dirname(__FILE__) . '/reporting_data_manager.class.php';
class ReportingTemplates
{

/**
 * Creates a reporting template registration in the database
 * @param array $props
 * @return ReportingTemplateRegistration
 */
    public static function create_reporting_template_registration($props)
    {
        $reporting_template_registration = new ReportingTemplateRegistration();
        $reporting_template_registration->set_default_properties($props);
        if(!$reporting_template_registration->create())
        {
            return false;
        }
        return $reporting_template_registration;
    }
}
?>