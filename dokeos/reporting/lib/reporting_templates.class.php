<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/reporting_template.class.php';
require_once dirname(__FILE__) . '/reporting_data_manager.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
class ReportingTemplates {
	
	public static function create_reporting_template($name,$application,$classname,$platform)
	{
		$reporting_template = new ReportingTemplate();
		$reporting_template->set_name($name);
		$reporting_template->set_application($application);
		$reporting_template->set_classname($classname);
		$reporting_template->set_platform($platform);
		if(!$reporting_template->create())
		{
			return false;
		}
		return $reporting_template;
	}
}
?>