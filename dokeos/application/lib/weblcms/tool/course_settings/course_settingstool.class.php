<?php
/**
 * $Id$
 * Course settings tool
 * @package application.weblcms.tool
 * @subpackage course_settings
 */
require_once dirname(__FILE__).'/../tool.class.php';
require_once dirname(__FILE__).'/../../course/courseform.class.php';

class CourseSettingsTool extends Tool
{
	function run()
	{
		$form = new CourseForm(CourseForm :: TYPE_EDIT, $this->get_parent()->get_course(), $this->get_url());
		
		if($form->validate())
		{
			$success = $form->update_course();
			$this->redirect(Weblcms :: ACTION_VIEW_WEBLCMS_HOME, get_lang($success ? 'CourseSettingsUpdated' : 'CourseSettingsUpdateFailed'), ($success ? false : true));
		}
		else
		{
			$this->display_header();
			$form->display();
			$this->display_footer();
		}
	}
}
?>