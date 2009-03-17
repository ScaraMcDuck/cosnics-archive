<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/reporting_template_registration.class.php';
require_once dirname(__FILE__) . '/reporting_data_manager.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
class ReportingTemplates {

    /**
     * Creates a reporting template registration in the database
     * @param array $props
     * @return ReportingTemplateRegistration
     */
	public static function create_reporting_template_registration($props)
	{
		$reporting_template_registration = new ReportingTemplateRegistration();
		$reporting_template_registration->set_title($props[ReportingTemplateRegistration :: PROPERTY_TITLE]);
		$reporting_template_registration->set_application($props[ReportingTemplateRegistration :: PROPERTY_APPLICATION]);
		$reporting_template_registration->set_classname($props[ReportingTemplateRegistration :: PROPERTY_CLASSNAME]);
		$reporting_template_registration->set_platform($props[ReportingTemplateRegistration :: PROPERTY_PLATFORM]);
        $reporting_template_registration->set_description($props[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION]);
		if(!$reporting_template_registration->create())
		{
			return false;
		}
		return $reporting_template_registration;
	}
}
?>