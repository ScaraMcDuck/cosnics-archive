<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
require_once dirname(__FILE__).'/../../course/courseform.class.php';

/**
 * Weblcms component allows the use to create a course
 */
class WeblcmsCourseCreatorComponent extends WeblcmsComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		if ($this->get_user()->is_platform_admin())
		{
			global $this_section;
			$this_section='platform_admin';
		}
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('CourseCreate'));
		
		if (!$this->get_user()->is_teacher() && !$this->get_user()->is_platform_admin())
		{
			$this->display_header($breadcrumbs);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$course = new Course();
		$course->set_visibility(COURSE_VISIBILITY_OPEN_WORLD);
		$course->set_subscribe_allowed(1);
		$course->set_unsubscribe_allowed(0);
		
		$user_info = $this->get_user();
		$course->set_language($user_info->get_language());
		
		$form = new CourseForm(CourseForm :: TYPE_CREATE, $course, $this->get_user(), $this->get_url());
		
		if($form->validate())
		{
			$success = $form->create_course();
			$this->redirect(Weblcms :: ACTION_VIEW_WEBLCMS_HOME, Translation :: get($success ? 'CourseCreated' : 'CourseNotCreated'), ($success ? false : true));
		}
		else
		{
			$this->display_header($breadcrumbs);
			$form->display();
			$this->display_footer();
		}
	}
}
?>