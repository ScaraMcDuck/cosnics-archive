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
		$trail = new BreadcrumbTrail();
		
		if (!$this->get_course()->is_course_admin($this->get_parent()->get_user_id()))
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$form = new CourseForm(CourseForm :: TYPE_EDIT, $this->get_course(), $this->get_user(), $this->get_url());
		
		if($form->validate())
		{
			$success = $form->update_course();
			$this->redirect(Weblcms :: ACTION_VIEW_WEBLCMS_HOME, Translation :: get($success ? 'CourseSettingsUpdated' : 'CourseSettingsUpdateFailed'), ($success ? false : true));
		}
		else
		{			
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>