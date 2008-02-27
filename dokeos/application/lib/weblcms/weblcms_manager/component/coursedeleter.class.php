<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
/**
 * Repository manager component which provides functionality to delete a course
 */
class WeblcmsCourseDeleterComponent extends WeblcmsComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$course_codes = $_GET[Weblcms :: PARAM_COURSE];
		$failures = 0;
		
		if (!$this->get_user()->is_platform_admin())
		{
			$breadcrumbs = array();
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get_lang('DeleteCourse'));
			$this->display_header($breadcrumbs);
			Display :: display_error_message(Translation :: get_lang("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		if (!empty ($course_codes))
		{
			if (!is_array($course_codes))
			{
				$course_codes = array ($course_codes);
			}
			
			foreach ($course_codes as $course_code)
			{
				$course = $this->get_parent()->retrieve_course($course_code);
				
				if (!$course->delete())
				{
					$failures++;
				}
			}
			
			if ($failures)
			{
				if (count($course_codes) == 1)
				{
					$message = 'SelectedCourseNotDeleted';
				}
				else
				{
					$message = 'SelectedCoursesNotDeleted';
				}
			}
			else
			{
				if (count($course_codes) == 1)
				{
					$message = 'SelectedCourseDeleted';
				}
				else
				{
					$message = 'SelectedCoursesDeleted';
				}
			}
			
			$this->redirect(null, Translation :: get_lang($message), ($failures ? true : false), array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_ADMIN_COURSE_BROWSER));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get_lang('NoCourseSelected')));
		}
	}
}
?>