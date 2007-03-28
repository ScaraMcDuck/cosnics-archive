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
		if (!api_is_allowed_to_create_course())
		{
			$breadcrumbs = array();
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('CourseCreate'));
			$this->display_header($breadcrumbs);
			Display :: display_error_message(get_lang("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$course = new Course();
		$course->set_category();
		$course->set_visibility(COURSE_VISIBILITY_OPEN_WORLD);
		$course->set_subscribe_allowed(1);
		$course->set_unsubscribe_allowed(0);
		
		$user_info = api_get_user_info();
		$course->set_language($user_info['language']);
		
		$form = new CourseForm(CourseForm :: TYPE_CREATE, $course, $this->get_url());
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('CourseCreate'));
		
		if($form->validate())
		{
			$success = $form->create_course();
			$this->redirect(Weblcms :: ACTION_VIEW_WEBLCMS_HOME, get_lang($success ? 'CourseCreated' : 'CourseNotCreated'), ($success ? false : true));
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