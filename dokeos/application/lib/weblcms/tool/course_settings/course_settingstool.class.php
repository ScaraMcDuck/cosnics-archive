<?php
/**
 * $Id$
 * Course settings tool
 * @package application.weblcms.tool
 * @subpackage course_settings
 */
require_once dirname(__FILE__).'/../tool.class.php';

class CourseSettingsTool extends Tool
{
	function run()
	{
		$this->display_header();
		echo 'Course Settings Tool';
		$this->display_footer();
	}
}
?>