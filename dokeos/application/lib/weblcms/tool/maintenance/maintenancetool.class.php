<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/../tool.class.php';

class MaintenanceTool extends Tool
{
	function run()
	{
		$this->display_header();
		echo 'Course Maintenance Tool';
		$this->display_footer();
	}
}
?>