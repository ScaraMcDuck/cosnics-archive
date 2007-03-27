<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
/**
 * Weblcms component which provides the user with a list
 * of all courses he or she has subscribed to.
 */
class WeblcmsHomeComponent extends WeblcmsComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('MyCourses'));
		$this->display_header($breadcrumbs);
		$courses = $this->retrieve_courses(api_get_user_id());
		
		echo '<ul>';
		while ($course = $courses->next_result())
		{
			echo '<li><a href="'. $this->get_course_viewing_url($course) .'">'.$course->get_name().'</a><br />'. $course->get_id() .' - '. $course->get_titular() .'</li>';
		}
		echo '</ul>';
		$this->display_footer();
	}
}
?>