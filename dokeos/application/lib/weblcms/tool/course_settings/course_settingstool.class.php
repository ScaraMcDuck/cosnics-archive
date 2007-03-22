<?php
/**
 * $Id$
 * Course settings tool
 * @package application.weblcms.tool
 * @subpackage course_settings
 */
require_once dirname(__FILE__).'/../tool.class.php';
require_once dirname(__FILE__).'/course_settingsform.class.php';

class CourseSettingsTool extends Tool
{
	function run()
	{
		$this->display_header();
		$form = new CourseSettingsForm($this);
		
		
		if($form->validate())
		{
			echo 'form submitted';
		}
		else
		{
			$form->display();
		}
		$this->display_footer();
	}
}
?>