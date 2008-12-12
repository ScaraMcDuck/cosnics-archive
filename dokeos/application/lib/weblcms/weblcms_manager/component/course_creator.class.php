<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcms_component.class.php';
require_once dirname(__FILE__).'/../../course/course_form.class.php';

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
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
		
		if (!$this->get_user()->is_teacher() && !$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$course = new Course();
		$course->set_visibility(COURSE_VISIBILITY_OPEN_WORLD);
		$course->set_subscribe_allowed(1);
		$course->set_unsubscribe_allowed(0);
		
		$user_info = $this->get_user();
		$course->set_language($user_info->get_language());
		$course->set_titular($user_info->get_id());
		
		$form = new CourseForm(CourseForm :: TYPE_CREATE, $course, $this->get_user(), $this->get_url());
		
		if($form->validate())
		{
			$success = $form->create_course();
			$this->redirect(null, Translation :: get($success ? 'CourseCreated' : 'CourseNotCreated'), ($success ? false : true), array('go' => Weblcms :: ACTION_VIEW_COURSE, 'course' => $course->get_id()));
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