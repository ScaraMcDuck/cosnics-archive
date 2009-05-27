<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms_manager.class.php';
require_once dirname(__FILE__).'/../weblcms_manager_component.class.php';
require_once dirname(__FILE__).'/../../course/course_form.class.php';

/**
 * Weblcms component allows the use to create a course
 */
class WeblcmsManagerCourseCreatorComponent extends WeblcmsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		if ($this->get_user()->is_platform_admin())
		{
			Header :: set_section('admin');
		}

		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array('go' => null, 'course' => null)), Translation :: get('MyCourses')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
		$trail->add_help('courses create');

		if (!$this->get_user()->is_teacher() && !$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail, false, true);
			echo '<div class="clear"></div><br />';
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
            if(WebLcmsDataManager :: get_instance()->retrieve_courses(null, new EqualityCondition(Course ::PROPERTY_VISUAL,$form->exportValue(Course :: PROPERTY_VISUAL)))->next_result())
			{
                  $this->display_header($trail, false, true);
                  $this->display_error_message(Translation :: get('CourseCodeAlreadyExists'));
                  $form->display();
                  $this->display_footer();
            }
            else
            {
                $success = $form->create_course();
                $this->redirect(Translation :: get($success ? 'CourseCreated' : 'CourseNotCreated'), ($success ? false : true), array('go' => WeblcmsManager :: ACTION_VIEW_COURSE, 'course' => $course->get_id()));
            }
		}
		else
		{
			$this->display_header($trail, false, true);
			echo '<div class="clear"></div><br />';
			$form->display();
			$this->display_footer();
		}
	}
}
?>