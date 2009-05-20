<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
//require_once dirname(__FILE__).'/../repository_tool.class.php';
require_once dirname(__FILE__).'/inc/maintenance_wizard.class.php';
/**
 * This tool implements some maintenance tools for a course.
 * It gives a course administrator the possibilities to copy course content,
 * remove publications from the course, create & restore backups,...
 */
class MaintenanceTool extends Tool
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		if (!$this->get_course()->is_course_admin($this->get_parent()->get_user()))
		{
			$this->display_header($trail, true, 'courses maintenance');
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$wizard = new MaintenanceWizard($this);
		$wizard->run();
	}
}
?>