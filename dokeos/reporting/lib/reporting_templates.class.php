<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/reporting_template.class.php';
require_once dirname(__FILE__) . '/reporting_data_manager.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
class ReportingTemplates {
	
	public static function create_reporting_template($props)
	{
		$reporting_template = new ReportingTemplate();
		$reporting_template->set_name($props['name']);
		$reporting_template->set_application($props['application']);
		$reporting_template->set_classname($props['classname']);
		$reporting_template->set_platform($props['platform']);
        $reporting_template->set_description($props['description']);
		if(!$reporting_template->create())
		{
			return false;
		}
		return $reporting_template;
	}
}
?>